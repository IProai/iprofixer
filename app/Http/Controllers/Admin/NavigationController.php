<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNavigationItemRequest;
use App\Http\Requests\UpdateNavigationItemRequest;
use App\Models\ContentPage;
use App\Models\NavigationItem;
use App\Models\NavigationMenu;
use App\Services\NavigationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class NavigationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user && $this->hasNavPermission($user, [
            'navigation.view',
            'navigation.edit',
            'navigation.publish',
            'navigation.delete',
            'content.manage',
        ]), 403);

        $menus = NavigationMenu::query()
            ->with(['items' => fn ($q) => $q->orderBy('sort_order')])
            ->get();

        $contentPages = ContentPage::query()->get();

        return view('admin.navigation.index', compact('menus', 'contentPages'));
    }

    public function storeItem(StoreNavigationItemRequest $request, NavigationService $navService): RedirectResponse
    {
        $validated = $request->validated();

        $targetBlank = ! empty($validated['target_blank']);
        $rel = $targetBlank ? 'noopener noreferrer' : null;

        $item = DB::transaction(function () use ($request, $validated, $targetBlank, $rel): NavigationItem {
            $item = NavigationItem::query()->create([
                'id' => (string) Str::uuid(),
                'navigation_menu_id' => $validated['navigation_menu_id'],
                'parent_id' => $validated['parent_id'] ?? null,
                'label_en' => $validated['label_en'],
                'label_ar' => $validated['label_ar'],
                'destination_type' => $validated['destination_type'],
                'route_name' => $validated['destination_type'] === 'internal_route' ? ($validated['route_name'] ?? null) : null,
                'content_page_id' => $validated['destination_type'] === 'content_page' ? ($validated['content_page_id'] ?? null) : null,
                'url' => $validated['destination_type'] === 'external_url' ? ($validated['url'] ?? null) : null,
                'sort_order' => isset($validated['sort_order']) ? (int) $validated['sort_order'] : 0,
                'is_active' => ! isset($validated['is_active']) || (bool) $validated['is_active'],
                'target_blank' => $targetBlank,
                'rel' => $rel,
            ]);

            $this->writeAudit($request, 'navigation.item.created', $item, null, $item->toArray());

            return $item;
        });

        $navService->clearCache();

        return back()->with('status', 'Navigation item created.');
    }

    public function editItem(Request $request, NavigationItem $item): View
    {
        $user = $request->user();
        abort_unless($user && $this->hasNavPermission($user, ['navigation.edit', 'content.manage']), 403);

        $availableParents = NavigationItem::query()
            ->where('navigation_menu_id', $item->navigation_menu_id)
            ->where('id', '!=', $item->id)
            ->whereNull('parent_id')
            ->get();

        $contentPages = ContentPage::query()->get();

        return view('admin.navigation.edit', compact('item', 'availableParents', 'contentPages'));
    }

    public function updateItem(UpdateNavigationItemRequest $request, NavigationItem $item, NavigationService $navService): RedirectResponse
    {
        $validated = $request->validated();

        if (! empty($validated['parent_id'])) {
            if ($validated['parent_id'] === $item->id) {
                throw ValidationException::withMessages([
                    'parent_id' => 'An item cannot be its own parent.',
                ]);
            }

            $childrenIds = $item->children()->pluck('id')->all();
            if (in_array($validated['parent_id'], $childrenIds, true)) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Circular navigation parent relationship is prohibited.',
                ]);
            }
        }

        $targetBlank = ! empty($validated['target_blank']);
        $rel = $targetBlank ? 'noopener noreferrer' : null;

        DB::transaction(function () use ($request, $validated, $item, $targetBlank, $rel): void {
            $before = $item->toArray();

            $item->update([
                'parent_id' => $validated['parent_id'] ?? null,
                'label_en' => $validated['label_en'],
                'label_ar' => $validated['label_ar'],
                'destination_type' => $validated['destination_type'],
                'route_name' => $validated['destination_type'] === 'internal_route' ? ($validated['route_name'] ?? null) : null,
                'content_page_id' => $validated['destination_type'] === 'content_page' ? ($validated['content_page_id'] ?? null) : null,
                'url' => $validated['destination_type'] === 'external_url' ? ($validated['url'] ?? null) : null,
                'sort_order' => isset($validated['sort_order']) ? (int) $validated['sort_order'] : $item->sort_order,
                'is_active' => isset($validated['is_active']) ? (bool) $validated['is_active'] : $item->is_active,
                'target_blank' => $targetBlank,
                'rel' => $rel,
            ]);

            $item->refresh();
            $this->writeAudit($request, 'navigation.item.updated', $item, $before, $item->toArray());
        });

        $navService->clearCache();

        return redirect()
            ->route('admin.navigation.index')
            ->with('status', 'Navigation item updated.');
    }

    public function toggleItem(Request $request, NavigationItem $item, NavigationService $navService): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $this->hasNavPermission($user, ['navigation.edit', 'content.manage']), 403);

        DB::transaction(function () use ($request, $item): void {
            $before = $item->toArray();
            $item->update(['is_active' => ! $item->is_active]);
            $action = $item->is_active ? 'navigation.item.activated' : 'navigation.item.deactivated';
            $this->writeAudit($request, $action, $item, $before, $item->toArray());
        });

        $navService->clearCache();

        return back()->with('status', 'Navigation item status updated.');
    }

    public function destroyItem(Request $request, NavigationItem $item, NavigationService $navService): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $this->hasNavPermission($user, ['navigation.delete', 'content.manage']), 403);

        DB::transaction(function () use ($request, $item): void {
            $before = $item->toArray();
            $item->delete();
            $this->writeAudit($request, 'navigation.item.deleted', $item, $before, null);
        });

        $navService->clearCache();

        return back()->with('status', 'Navigation item deleted.');
    }

    /**
     * @param  object  $user
     * @param  list<string>  $permissions
     */
    private function hasNavPermission($user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($user->can($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>|null  $before
     * @param  array<string, mixed>|null  $after
     */
    private function writeAudit(
        Request $request,
        string $action,
        NavigationItem $item,
        ?array $before,
        ?array $after,
    ): void {
        DB::table('audit_events')->insert([
            'id' => (string) Str::uuid(),
            'actor_id' => $request->user()?->getKey(),
            'action' => $action,
            'subject_type' => NavigationItem::class,
            'subject_id' => (string) $item->getKey(),
            'correlation_id' => (string) Str::uuid(),
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 1000),
            'before' => $before === null ? null : json_encode($before, JSON_THROW_ON_ERROR),
            'after' => $after === null ? null : json_encode($after, JSON_THROW_ON_ERROR),
            'metadata' => json_encode(['source' => 'admin.navigation'], JSON_THROW_ON_ERROR),
            'occurred_at' => now(),
        ]);
    }
}
