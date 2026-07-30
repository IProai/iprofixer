<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContentPageRequest;
use App\Http\Requests\UpdateContentPageRequest;
use App\Models\ContentPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $payload = $request->validated();
        $payload['created_by'] = $request->user()->getKey();
        $payload['updated_by'] = $request->user()->getKey();
        $payload['published_at'] = $payload['status'] === 'published' ? now() : null;

        $page = ContentPage::query()->create($payload);

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
        $payload = $request->validated();
        $payload['updated_by'] = $request->user()->getKey();
        $payload['published_at'] = $payload['status'] === 'published'
            ? ($contentPage->published_at ?? now())
            : null;

        $contentPage->update($payload);

        return back()->with('status', 'Content page updated.');
    }

    public function destroy(Request $request, ContentPage $contentPage): RedirectResponse
    {
        abort_unless($request->user()?->can('content.manage'), 403);

        $contentPage->delete();

        return redirect()
            ->route('admin.content-pages.index')
            ->with('status', 'Content page archived.');
    }
}
