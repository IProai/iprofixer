<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaAssetRequest;
use App\Http\Requests\UpdateMediaAssetRequest;
use App\Models\MediaAsset;
use App\Services\MediaUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class MediaAssetController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user && $this->hasMediaPermission($user, [
            'media.view',
            'media.upload',
            'media.edit',
            'media.archive',
            'media.restore',
            'media.delete',
            'content.manage',
        ]), 403);

        $query = MediaAsset::query()->latest();

        if ($request->filled('search')) {
            $search = (string) $request->query('search');
            $query->where(function ($q) use ($search): void {
                $q->where('original_name', 'like', "%{$search}%")
                    ->orWhere('alt_text_en', 'like', "%{$search}%")
                    ->orWhere('alt_text_ar', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('usage_status', $request->query('status'));
        }

        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }

        $assets = $query->paginate(24);

        return view('admin.media.index', compact('assets'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        abort_unless($user && $this->hasMediaPermission($user, ['media.upload', 'content.manage']), 403);

        return view('admin.media.create');
    }

    public function store(StoreMediaAssetRequest $request, MediaUploadService $uploader): RedirectResponse
    {
        $validated = $request->validated();
        $isDecorative = ! empty($validated['is_decorative']);

        if (! $isDecorative) {
            $altEn = trim((string) ($validated['alt_text_en'] ?? ''));
            $altAr = trim((string) ($validated['alt_text_ar'] ?? ''));

            if ($altEn === '' || $altAr === '') {
                throw ValidationException::withMessages([
                    'alt_text_en' => 'Both English and Arabic alt text are required for non-decorative assets.',
                    'alt_text_ar' => 'كل من نص التوضيح بالإنجليزية والعربية مطلوب للأصول غير الزخرفية.',
                ]);
            }

            $dummyAsset = new MediaAsset(['original_name' => $request->file('file')->getClientOriginalName()]);
            if ($dummyAsset->isMeaninglessAltText($altEn) || $dummyAsset->isMeaninglessAltText($altAr)) {
                throw ValidationException::withMessages([
                    'alt_text_en' => 'Alt text must be meaningful and cannot be generic filenames or keywords.',
                ]);
            }
        }

        $asset = DB::transaction(function () use ($request, $validated, $uploader): MediaAsset {
            $asset = $uploader->upload(
                $request->file('file'),
                $request->user(),
                $validated
            );

            $this->writeAudit($request, 'media.asset.uploaded', $asset, null, $asset->toArray());

            return $asset;
        });

        return redirect()
            ->route('admin.media.edit', $asset)
            ->with('status', 'Media asset uploaded successfully.');
    }

    public function edit(Request $request, MediaAsset $medium): View
    {
        $user = $request->user();
        abort_unless($user && $this->hasMediaPermission($user, ['media.edit', 'content.manage']), 403);

        $references = $medium->getReferences();

        return view('admin.media.edit', compact('medium', 'references'));
    }

    public function update(UpdateMediaAssetRequest $request, MediaAsset $medium): RedirectResponse
    {
        $validated = $request->validated();
        $isDecorative = ! empty($validated['is_decorative']);

        if (! $isDecorative) {
            $altEn = trim((string) ($validated['alt_text_en'] ?? ''));
            $altAr = trim((string) ($validated['alt_text_ar'] ?? ''));

            if ($altEn === '' || $altAr === '') {
                throw ValidationException::withMessages([
                    'alt_text_en' => 'Both English and Arabic alt text are required for non-decorative assets.',
                ]);
            }

            if ($medium->isMeaninglessAltText($altEn) || $medium->isMeaninglessAltText($altAr)) {
                throw ValidationException::withMessages([
                    'alt_text_en' => 'Alt text must be meaningful and cannot be generic filenames or keywords.',
                ]);
            }
        }

        DB::transaction(function () use ($request, $validated, $medium, $isDecorative): void {
            $before = $medium->toArray();

            $usageStatus = ($isDecorative || (! empty($validated['alt_text_en']) && ! empty($validated['alt_text_ar'])))
                ? 'approved'
                : 'pending';

            $medium->update([
                'is_decorative' => $isDecorative,
                'alt_text_en' => $isDecorative ? null : ($validated['alt_text_en'] ?? null),
                'alt_text_ar' => $isDecorative ? null : ($validated['alt_text_ar'] ?? null),
                'caption_en' => $validated['caption_en'] ?? null,
                'caption_ar' => $validated['caption_ar'] ?? null,
                'source_owner' => $validated['source_owner'] ?? null,
                'usage_status' => $usageStatus,
                'focal_x' => isset($validated['focal_x']) ? (float) $validated['focal_x'] : $medium->focal_x,
                'focal_y' => isset($validated['focal_y']) ? (float) $validated['focal_y'] : $medium->focal_y,
            ]);

            $medium->refresh();
            $this->writeAudit($request, 'media.asset.updated', $medium, $before, $medium->toArray());
        });

        return back()->with('status', 'Media metadata updated.');
    }

    public function destroy(Request $request, MediaAsset $medium): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $this->hasMediaPermission($user, ['media.archive', 'content.manage']), 403);

        DB::transaction(function () use ($request, $medium): void {
            $before = $medium->toArray();
            $medium->delete();
            $this->writeAudit($request, 'media.asset.archived', $medium, $before, null);
        });

        return redirect()
            ->route('admin.media.index')
            ->with('status', 'Media asset archived.');
    }

    public function restore(Request $request, string $id): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $this->hasMediaPermission($user, ['media.restore', 'content.manage']), 403);

        /** @var MediaAsset $asset */
        $asset = MediaAsset::onlyTrashed()->findOrFail($id);

        DB::transaction(function () use ($request, $asset): void {
            $asset->restore();
            $this->writeAudit($request, 'media.asset.restored', $asset, null, $asset->toArray());
        });

        return redirect()
            ->route('admin.media.edit', $asset)
            ->with('status', 'Media asset restored.');
    }

    public function forceDelete(Request $request, string $id): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $this->hasMediaPermission($user, ['media.delete', 'content.manage']), 403);

        /** @var MediaAsset $asset */
        $asset = MediaAsset::withTrashed()->findOrFail($id);

        if ($asset->isReferenced()) {
            $this->writeAudit(
                $request,
                'media.asset.delete_blocked',
                $asset,
                $asset->toArray(),
                ['blocked_reason' => 'in_use', 'references' => $asset->getReferences()]
            );

            throw ValidationException::withMessages([
                'deletion' => "Cannot permanently delete media asset #{$asset->id} because it is currently referenced in active content or proof records.",
            ]);
        }

        DB::transaction(function () use ($request, $asset): void {
            $before = $asset->toArray();

            Storage::disk($asset->disk)->delete($asset->path);
            $asset->forceDelete();

            $this->writeAudit($request, 'media.asset.deleted', $asset, $before, null);
        });

        return redirect()
            ->route('admin.media.index')
            ->with('status', 'Media asset permanently deleted.');
    }

    public function picker(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user && $this->hasMediaPermission($user, [
            'media.view',
            'media.upload',
            'media.edit',
            'media.archive',
            'media.restore',
            'media.delete',
            'content.manage',
        ]), 403);

        $assets = MediaAsset::query()
            ->where('usage_status', 'approved')
            ->latest()
            ->get()
            ->map(fn (MediaAsset $asset): array => [
                'id' => $asset->id,
                'name' => $asset->original_name,
                'url' => $asset->getUrl(),
                'alt_text_en' => $asset->alt_text_en,
                'alt_text_ar' => $asset->alt_text_ar,
                'is_decorative' => $asset->is_decorative,
                'focal_x' => $asset->focal_x,
                'focal_y' => $asset->focal_y,
            ]);

        return response()->json(['assets' => $assets]);
    }

    /**
     * @param  object  $user
     * @param  list<string>  $permissions
     */
    private function hasMediaPermission($user, array $permissions): bool
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
        MediaAsset $asset,
        ?array $before,
        ?array $after,
    ): void {
        DB::table('audit_events')->insert([
            'id' => (string) Str::uuid(),
            'actor_id' => $request->user()?->getKey(),
            'action' => $action,
            'subject_type' => MediaAsset::class,
            'subject_id' => (string) $asset->getKey(),
            'correlation_id' => (string) Str::uuid(),
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 1000),
            'before' => $before === null ? null : json_encode($before, JSON_THROW_ON_ERROR),
            'after' => $after === null ? null : json_encode($after, JSON_THROW_ON_ERROR),
            'metadata' => json_encode(['source' => 'admin.media'], JSON_THROW_ON_ERROR),
            'occurred_at' => now(),
        ]);
    }
}
