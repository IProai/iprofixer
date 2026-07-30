<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContentPageRequest;
use App\Http\Requests\UpdateContentPageRequest;
use App\Models\ContentPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class ContentPageController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->can('content.manage'), 403);

        $pages = ContentPage::query()
            ->latest('updated_at')
            ->paginate(20);

        return view('admin.content-pages.index', compact('pages'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()?->can('content.manage'), 403);

        return view('admin.content-pages.create');
    }

    public function store(StoreContentPageRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $page = DB::transaction(function () use ($request, $validated): ContentPage {
            $page = ContentPage::query()->create([
                ...Arr::only($validated, ['slug', 'type', 'status']),
                'created_by' => $request->user()->getKey(),
                'updated_by' => $request->user()->getKey(),
                'published_at' => $validated['status'] === 'published' ? now() : null,
            ]);

            $this->persistTranslations($page, $validated);
            $this->writeAudit($request, 'content.page.created', $page, null, $page->fresh()->toArray());

            return $page;
        });

        return redirect()
            ->route('admin.content-pages.edit', $page)
            ->with('status', 'Content page created.');
    }

    public function edit(Request $request, ContentPage $contentPage): View
    {
        abort_unless($request->user()?->can('content.manage'), 403);

        return view('admin.content-pages.edit', compact('contentPage'));
    }

    public function update(UpdateContentPageRequest $request, ContentPage $contentPage): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated, $contentPage): void {
            $before = $contentPage->toArray();

            $contentPage->update([
                ...Arr::only($validated, ['slug', 'type', 'status']),
                'updated_by' => $request->user()->getKey(),
                'published_at' => $validated['status'] === 'published'
                    ? ($contentPage->published_at ?? now())
                    : null,
            ]);

            $this->persistTranslations($contentPage, $validated);
            $contentPage->refresh();

            $this->writeAudit($request, 'content.page.updated', $contentPage, $before, $contentPage->toArray());
        });

        return back()->with('status', 'Content page updated.');
    }

    public function destroy(Request $request, ContentPage $contentPage): RedirectResponse
    {
        abort_unless($request->user()?->can('content.manage'), 403);

        DB::transaction(function () use ($request, $contentPage): void {
            $before = $contentPage->toArray();
            $contentPage->delete();
            $this->writeAudit($request, 'content.page.archived', $contentPage, $before, null);
        });

        return redirect()
            ->route('admin.content-pages.index')
            ->with('status', 'Content page archived.');
    }

    /** @param array<string, mixed> $validated */
    private function persistTranslations(ContentPage $page, array $validated): void
    {
        foreach (['en', 'ar'] as $locale) {
            $page->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title' => $validated["title_{$locale}"],
                    'summary' => $validated["summary_{$locale}"] ?? null,
                    'body' => $validated["body_{$locale}"],
                    'seo_title' => $validated["seo_title_{$locale}"] ?? null,
                    'seo_description' => $validated["seo_description_{$locale}"] ?? null,
                    'translation_approved' => true,
                ],
            );
        }

        $page->unsetRelation('translations');
        $page->load('translations');
    }

    /**
     * @param array<string, mixed>|null $before
     * @param array<string, mixed>|null $after
     */
    private function writeAudit(
        Request $request,
        string $action,
        ContentPage $page,
        ?array $before,
        ?array $after,
    ): void {
        DB::table('audit_events')->insert([
            'id' => (string) Str::uuid(),
            'actor_id' => $request->user()?->getKey(),
            'action' => $action,
            'subject_type' => ContentPage::class,
            'subject_id' => (string) $page->getKey(),
            'correlation_id' => (string) Str::uuid(),
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 1000),
            'before' => $before === null ? null : json_encode($before, JSON_THROW_ON_ERROR),
            'after' => $after === null ? null : json_encode($after, JSON_THROW_ON_ERROR),
            'metadata' => json_encode(['source' => 'admin.cms'], JSON_THROW_ON_ERROR),
            'occurred_at' => now(),
        ]);
    }
}
