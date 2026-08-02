<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class MediaAsset extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'disk',
        'path',
        'visibility',
        'mime_type',
        'size_bytes',
        'original_name',
        'extension',
        'checksum',
        'width',
        'height',
        'alt_text_en',
        'alt_text_ar',
        'is_decorative',
        'caption_en',
        'caption_ar',
        'source_owner',
        'usage_status',
        'focal_x',
        'focal_y',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'is_decorative' => 'boolean',
            'focal_x' => 'float',
            'focal_y' => 'float',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isApproved(): bool
    {
        if ($this->is_decorative) {
            return true;
        }

        return ! empty(trim((string) $this->alt_text_en))
            && ! empty(trim((string) $this->alt_text_ar))
            && ! $this->isMeaninglessAltText((string) $this->alt_text_en)
            && ! $this->isMeaninglessAltText((string) $this->alt_text_ar);
    }

    public function isMeaninglessAltText(string $alt): bool
    {
        $normalized = strtolower(trim($alt));
        $meaningless = [
            'image',
            'photo',
            'picture',
            'img',
            'file',
            'test',
            'untitled',
            'sample',
            strtolower((string) $this->original_name),
        ];

        return in_array($normalized, $meaningless, true)
            || str_ends_with($normalized, '.jpg')
            || str_ends_with($normalized, '.png')
            || str_ends_with($normalized, '.jpeg')
            || str_ends_with($normalized, '.webp');
    }

    public function isReferenced(): bool
    {
        return count($this->getReferences()) > 0;
    }

    /** @return list<array{type: string, id: string|int, label: string}> */
    public function getReferences(): array
    {
        $references = [];

        $proofItems = DB::table('proof_items')
            ->where('media_asset_id', $this->id)
            ->get();

        foreach ($proofItems as $proof) {
            $references[] = [
                'type' => 'ProofItem',
                'id' => $proof->id,
                'label' => $proof->title_en ?? "Proof #{$proof->id}",
            ];
        }

        $matchingTranslations = DB::table('content_translations')
            ->where('body', 'like', "%{$this->id}%")
            ->orWhere('body', 'like', "%{$this->path}%")
            ->get();

        foreach ($matchingTranslations as $trans) {
            $references[] = [
                'type' => 'ContentTranslation',
                'id' => $trans->id,
                'label' => "Translation #{$trans->id} ({$trans->locale})",
            ];
        }

        return $references;
    }

    public function getUrl(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }
}
