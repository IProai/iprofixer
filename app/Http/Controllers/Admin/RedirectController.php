<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRedirectRuleRequest;
use App\Http\Requests\UpdateRedirectRuleRequest;
use App\Models\ContentPage;
use App\Models\RedirectRule;
use App\Services\RedirectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class RedirectController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user && $this->hasRedirectPermission($user, [
            'redirects.view',
            'redirects.create',
            'redirects.edit',
            'redirects.activate',
            'redirects.delete',
            'content.manage',
        ]), 403);

        $query = RedirectRule::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search): void {
                $q->where('source_path', 'like', "%{$search}%")
                    ->orWhere('destination_path', 'like', "%{$search}%")
                    ->orWhere('note', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $redirects = $query->orderByDesc('created_at')->paginate(20);
        $contentPages = ContentPage::query()->get();

        return view('admin.redirects.index', compact('redirects', 'contentPages'));
    }

    public function store(StoreRedirectRuleRequest $request, RedirectService $redirectService): RedirectResponse
    {
        $validated = $request->validated();
        $sourcePath = RedirectRule::normalizePath($validated['source_path']);

        $rule = DB::transaction(function () use ($request, $validated, $sourcePath): RedirectRule {
            $rule = RedirectRule::query()->create([
                'id' => (string) Str::uuid(),
                'source_path' => $sourcePath,
                'destination_type' => $validated['destination_type'],
                'destination_path' => $validated['destination_path'],
                'route_name' => $validated['route_name'] ?? null,
                'content_page_id' => $validated['content_page_id'] ?? null,
                'status_code' => (int) $validated['status_code'],
                'is_active' => ! isset($validated['is_active']) || (bool) $validated['is_active'],
                'locale' => $validated['locale'] ?? null,
                'note' => $validated['note'] ?? null,
                'created_by' => $request->user()?->getKey(),
            ]);

            $this->writeAudit($request, 'redirect.rule.created', $rule, null, $rule->toArray());

            return $rule;
        });

        $redirectService->clearSitemapCache();

        return back()->with('status', 'Redirect rule created successfully.');
    }

    public function edit(Request $request, RedirectRule $redirect): View
    {
        $user = $request->user();
        abort_unless($user && $this->hasRedirectPermission($user, ['redirects.edit', 'content.manage']), 403);

        $contentPages = ContentPage::query()->get();

        return view('admin.redirects.edit', ['rule' => $redirect, 'contentPages' => $contentPages]);
    }

    public function update(UpdateRedirectRuleRequest $request, RedirectRule $redirect, RedirectService $redirectService): RedirectResponse
    {
        $validated = $request->validated();
        $sourcePath = RedirectRule::normalizePath($validated['source_path']);

        DB::transaction(function () use ($request, $validated, $redirect, $sourcePath): void {
            $before = $redirect->toArray();

            $redirect->update([
                'source_path' => $sourcePath,
                'destination_type' => $validated['destination_type'],
                'destination_path' => $validated['destination_path'],
                'route_name' => $validated['route_name'] ?? null,
                'content_page_id' => $validated['content_page_id'] ?? null,
                'status_code' => (int) $validated['status_code'],
                'is_active' => isset($validated['is_active']) ? (bool) $validated['is_active'] : $redirect->is_active,
                'locale' => $validated['locale'] ?? null,
                'note' => $validated['note'] ?? null,
                'updated_by' => $request->user()?->getKey(),
            ]);

            $redirect->refresh();
            $this->writeAudit($request, 'redirect.rule.updated', $redirect, $before, $redirect->toArray());
        });

        $redirectService->clearSitemapCache();

        return redirect()
            ->route('admin.redirects.index')
            ->with('status', 'Redirect rule updated successfully.');
    }

    public function toggle(Request $request, RedirectRule $redirect, RedirectService $redirectService): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $this->hasRedirectPermission($user, ['redirects.activate', 'redirects.edit', 'content.manage']), 403);

        DB::transaction(function () use ($request, $redirect): void {
            $before = $redirect->toArray();
            $redirect->update(['is_active' => ! $redirect->is_active]);
            $action = $redirect->is_active ? 'redirect.rule.activated' : 'redirect.rule.deactivated';
            $this->writeAudit($request, $action, $redirect, $before, $redirect->toArray());
        });

        $redirectService->clearSitemapCache();

        return back()->with('status', 'Redirect rule status updated.');
    }

    public function destroy(Request $request, RedirectRule $redirect, RedirectService $redirectService): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $this->hasRedirectPermission($user, ['redirects.delete', 'content.manage']), 403);

        DB::transaction(function () use ($request, $redirect): void {
            $before = $redirect->toArray();
            $redirect->delete();
            $this->writeAudit($request, 'redirect.rule.deleted', $redirect, $before, null);
        });

        $redirectService->clearSitemapCache();

        return back()->with('status', 'Redirect rule deleted.');
    }

    public function testResolution(Request $request, RedirectService $redirectService): JsonResponse
    {
        $path = (string) $request->input('path', '/');
        $rule = $redirectService->findActiveRedirect($path);

        if (! $rule) {
            return response()->json([
                'found' => false,
                'path' => RedirectRule::normalizePath($path),
                'message' => 'No active redirect rule found for this path.',
            ]);
        }

        return response()->json([
            'found' => true,
            'source_path' => $rule->source_path,
            'destination_url' => $rule->resolveDestinationUrl(),
            'status_code' => $rule->status_code,
            'is_active' => $rule->is_active,
            'hit_count' => $rule->hit_count,
        ]);
    }

    /** @param list<string> $permissions */
    private function hasRedirectPermission(object $user, array $permissions): bool
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
        RedirectRule $rule,
        ?array $before,
        ?array $after,
    ): void {
        DB::table('audit_events')->insert([
            'id' => (string) Str::uuid(),
            'actor_id' => $request->user()?->getKey(),
            'action' => $action,
            'subject_type' => RedirectRule::class,
            'subject_id' => (string) $rule->getKey(),
            'correlation_id' => (string) Str::uuid(),
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 1000),
            'before' => $before === null ? null : json_encode($before, JSON_THROW_ON_ERROR),
            'after' => $after === null ? null : json_encode($after, JSON_THROW_ON_ERROR),
            'metadata' => json_encode(['source' => 'admin.redirects'], JSON_THROW_ON_ERROR),
            'occurred_at' => now(),
        ]);
    }
}
