<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContentPageRequest;
use App\Http\Requests\UpdateContentPageRequest;
use App\Models\ContentPage;
use App\Models\ContentPageRevision;
use App\Services\RedirectService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class ContentPageController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user && $this->hasCmsPermission($user, [
            'content.create',
            'content.edit',
            'content.review',
            'content.approve',
            'content.publish',
            'content.preview',
            'content.restore',
            'content.archive',
            'content.manage',
        ]), 403);

        $pages = ContentPage::query()
            ->latest('updated_at')
            ->paginate(20);

        return view('admin.content-pages.index', compact('pages'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        abort_unless($user && $this->hasCmsPermission($user, ['content.create', 'content.manage']), 403);

        return view('admin.content-pages.create');
    }

    public function store(StoreContentPageRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (in_array($validated['status'], ['approved', 'scheduled', 'published'], true)) {
            $approveEn = ! empty($validated['approve_en']);
            $approveAr = ! empty($validated['approve_ar']);

            if (! $approveEn || ! $approveAr) {
                throw ValidationException::withMessages([
                    'status' => 'Both English and Arabic translations must be approved prior to approval or publication.',
                ]);
            }
        }

        $page = DB::transaction(function () use ($request, $validated): ContentPage {
            $scheduledFor = ! empty($validated['scheduled_for'])
                ? CarbonImmutable::parse($validated['scheduled_for'])
                : null;

            $page = ContentPage::query()->create([
                ...Arr::only($validated, ['slug', 'type', 'status']),
                'created_by' => $request->user()->getKey(),
                'updated_by' => $request->user()->getKey(),
                'scheduled_for' => $scheduledFor,
                'published_at' => $validated['status'] === 'published' ? now() : null,
            ]);

            $this->persistTranslations($page, $validated);
            $page->refresh();

            $this->createRevision($page, $request, 'Initial content revision.');
            $this->writeAudit($request, 'content.page.created', $page, null, $page->toArray());

            return $page;
        });

        return redirect()
            ->route('admin.content-pages.edit', $page)
            ->with('status', 'Content page created.');
    }

    public function edit(Request $request, ContentPage $contentPage): View
    {
        $user = $request->user();
        abort_unless($user && $this->hasCmsPermission($user, ['content.edit', 'content.manage']), 403);

        $contentPage->load(['revisions.author', 'translations']);

        return view('admin.content-pages.edit', compact('contentPage'));
    }

    public function update(UpdateContentPageRequest $request, ContentPage $contentPage): RedirectResponse
    {
        $validated = $request->validated();

        if (in_array($validated['status'], ['approved', 'scheduled', 'published'], true)) {
            $approveEn = isset($validated['approve_en']) ? (bool) $validated['approve_en'] : $contentPage->isTranslationApproved('en');
            $approveAr = isset($validated['approve_ar']) ? (bool) $validated['approve_ar'] : $contentPage->isTranslationApproved('ar');

            if (! $approveEn || ! $approveAr) {
                throw ValidationException::withMessages([
                    'status' => 'Both English and Arabic translations must be approved prior to approval or publication.',
                ]);
            }
        }

        $oldSlug = $contentPage->slug;

        DB::transaction(function () use ($request, $validated, $contentPage, $oldSlug): void {
            $before = $contentPage->toArray();

            $scheduledFor = ! empty($validated['scheduled_for'])
                ? CarbonImmutable::parse($validated['scheduled_for'])
                : $contentPage->scheduled_for;

            $contentPage->update([
                ...Arr::only($validated, ['slug', 'type', 'status']),
                'updated_by' => $request->user()->getKey(),
                'scheduled_for' => $scheduledFor,
                'published_at' => $validated['status'] === 'published'
                    ? ($contentPage->published_at ?? now())
                    : null,
            ]);

            $this->persistTranslations($contentPage, $validated);
            $contentPage->refresh();

            if ($oldSlug !== $contentPage->slug) {
                app(RedirectService::class)->createAutoRedirect($contentPage, $oldSlug);
            }

            $this->createRevision($contentPage, $request, 'Content updated.');
            $this->writeAudit($request, 'content.page.updated', $contentPage, $before, $contentPage->toArray());
        });

        return back()->with('status', 'Content page updated.');
    }

    public function destroy(Request $request, ContentPage $contentPage): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $this->hasCmsPermission($user, ['content.archive', 'content.manage']), 403);

        DB::transaction(function () use ($request, $contentPage): void {
            $before = $contentPage->toArray();
            $contentPage->delete();
            $this->writeAudit($request, 'content.page.archived', $contentPage, $before, null);
        });

        return redirect()
            ->route('admin.content-pages.index')
            ->with('status', 'Content page archived.');
    }

    public function approve(Request $request, ContentPage $contentPage): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $this->hasCmsPermission($user, ['content.approve', 'content.manage']), 403);

        if (! $contentPage->isBilinguallyApproved()) {
            throw ValidationException::withMessages([
                'approval' => 'Both English and Arabic translations must be explicitly approved before approving the content page.',
            ]);
        }

        DB::transaction(function () use ($request, $contentPage): void {
            $before = $contentPage->toArray();
            $contentPage->update([
                'status' => 'approved',
                'updated_by' => $request->user()->getKey(),
            ]);
            $contentPage->refresh();
            $this->createRevision($contentPage, $request, 'Content page approved for publication.');
            $this->writeAudit($request, 'content.page.approved', $contentPage, $before, $contentPage->toArray());
        });

        return back()->with('status', 'Content page approved.');
    }

    public function publish(Request $request, ContentPage $contentPage): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $this->hasCmsPermission($user, ['content.publish', 'content.manage']), 403);

        if (! $contentPage->isBilinguallyApproved()) {
            throw ValidationException::withMessages([
                'publication' => 'Both English and Arabic translations must be approved before publication.',
            ]);
        }

        DB::transaction(function () use ($request, $contentPage): void {
            $before = $contentPage->toArray();
            $contentPage->update([
                'status' => 'published',
                'published_at' => now(),
                'scheduled_for' => null,
                'updated_by' => $request->user()->getKey(),
            ]);
            $contentPage->refresh();
            $this->createRevision($contentPage, $request, 'Content page published.');
            $this->writeAudit($request, 'content.page.published', $contentPage, $before, $contentPage->toArray());
        });

        return back()->with('status', 'Content page published.');
    }

    public function unpublish(Request $request, ContentPage $contentPage): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $this->hasCmsPermission($user, ['content.publish', 'content.manage']), 403);

        DB::transaction(function () use ($request, $contentPage): void {
            $before = $contentPage->toArray();
            $contentPage->update([
                'status' => 'draft',
                'published_at' => null,
                'updated_by' => $request->user()->getKey(),
            ]);
            $contentPage->refresh();
            $this->createRevision($contentPage, $request, 'Content page unpublished.');
            $this->writeAudit($request, 'content.page.unpublished', $contentPage, $before, $contentPage->toArray());
        });

        return back()->with('status', 'Content page unpublished.');
    }

    public function preview(Request $request, ContentPage $contentPage): Response|View
    {
        $user = $request->user();
        abort_unless($user && $this->hasCmsPermission($user, [
            'content.preview',
            'content.edit',
            'content.review',
            'content.approve',
            'content.publish',
            'content.manage',
        ]), 403);

        $view = view('page', [
            'page' => $contentPage->type === 'page' ? $contentPage->slug : 'services',
            'slug' => $contentPage->slug,
            'cmsPage' => $contentPage,
            'isPreview' => true,
        ]);

        return response($view)
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }

    public function restore(Request $request, ContentPage $contentPage, ContentPageRevision $revision): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $this->hasCmsPermission($user, ['content.restore', 'content.manage']), 403);

        abort_unless($revision->content_page_id === $contentPage->id, 404);

        DB::transaction(function () use ($request, $contentPage, $revision): void {
            $before = $contentPage->toArray();
            $snapshot = $revision->snapshot;

            $contentPage->update([
                'slug' => $snapshot['slug'] ?? $contentPage->slug,
                'type' => $snapshot['type'] ?? $contentPage->type,
                'status' => 'draft',
                'published_at' => null,
                'scheduled_for' => null,
                'updated_by' => $request->user()->getKey(),
            ]);

            if (isset($snapshot['translations']) && is_array($snapshot['translations'])) {
                foreach ($snapshot['translations'] as $t) {
                    $contentPage->translations()->updateOrCreate(
                        ['locale' => $t['locale']],
                        [
                            'title' => $t['title'],
                            'summary' => $t['summary'] ?? null,
                            'body' => $t['body'],
                            'seo_title' => $t['seo_title'] ?? null,
                            'seo_description' => $t['seo_description'] ?? null,
                            'translation_approved' => (bool) ($t['translation_approved'] ?? false),
                        ],
                    );
                }
            }

            $contentPage->refresh();

            $this->createRevision(
                $contentPage,
                $request,
                "Restored from revision #{$revision->revision_number}."
            );

            $this->writeAudit(
                $request,
                'content.page.restored',
                $contentPage,
                $before,
                $contentPage->toArray()
            );
        });

        return back()->with('status', "Content restored from revision #{$revision->revision_number}.");
    }

    /**
     * @param  object  $user
     * @param  list<string>  $permissions
     */
    private function hasCmsPermission($user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($user->can($permission)) {
                return true;
            }
        }

        return false;
    }

    /** @param array<string, mixed> $validated */
    private function persistTranslations(ContentPage $page, array $validated): void
    {
        foreach (['en', 'ar'] as $locale) {
            $approved = isset($validated["approve_{$locale}"])
                ? (bool) $validated["approve_{$locale}"]
                : ($page->translation($locale)?->translation_approved ?? false);

            $page->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title' => $validated["title_{$locale}"],
                    'summary' => $validated["summary_{$locale}"] ?? null,
                    'body' => $validated["body_{$locale}"],
                    'seo_title' => $validated["seo_title_{$locale}"] ?? null,
                    'seo_description' => $validated["seo_description_{$locale}"] ?? null,
                    'translation_approved' => $approved,
                ],
            );
        }

        $page->unsetRelation('translations');
        $page->load('translations');
    }

    private function createRevision(ContentPage $page, Request $request, string $summary): void
    {
        $revisionNumber = ((int) $page->revisions()->max('revision_number')) + 1;

        $page->revisions()->create([
            'created_by' => $request->user()?->getKey(),
            'revision_number' => $revisionNumber,
            'status' => $page->status,
            'snapshot' => $page->toArray(),
            'change_summary' => $summary,
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $before
     * @param  array<string, mixed>|null  $after
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
