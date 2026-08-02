<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MediaAsset;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class MediaUploadService
{
    /** @var list<string> */
    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'];

    /** @var list<string> */
    public const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/avif',
        'image/gif',
    ];

    public const MAX_SIZE_BYTES = 10 * 1024 * 1024; // 10MB

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function upload(
        UploadedFile $file,
        ?User $uploader = null,
        array $metadata = [],
        string $disk = 'public'
    ): MediaAsset {
        $clientExtension = strtolower($file->getClientOriginalExtension());
        $originalName = basename($file->getClientOriginalName());

        if ($clientExtension === 'svg' || strtolower((string) $file->getMimeType()) === 'image/svg+xml') {
            throw ValidationException::withMessages([
                'file' => 'SVG file uploads are explicitly disallowed for security governance.',
            ]);
        }

        if (! in_array($clientExtension, self::ALLOWED_EXTENSIONS, true)) {
            throw ValidationException::withMessages([
                'file' => "The extension .{$clientExtension} is not permitted.",
            ]);
        }

        $doubleExtensionPattern = '/\.(php|phtml|exe|sh|bat|cmd|js|html|htm|cgi|py|pl|rb)\./i';
        if (preg_match($doubleExtensionPattern, $originalName) === 1) {
            throw ValidationException::withMessages([
                'file' => 'Executable script file upload attempt rejected.',
            ]);
        }

        if ($file->getSize() > self::MAX_SIZE_BYTES) {
            throw ValidationException::withMessages([
                'file' => 'The uploaded file exceeds the 10MB limit.',
            ]);
        }

        $detectedMime = null;
        if (function_exists('finfo_open') && $file->getRealPath()) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo !== false) {
                $detectedMime = finfo_file($finfo, $file->getRealPath());
                finfo_close($finfo);
            }
        }
        $mime = $detectedMime ?: $file->getMimeType();

        if (! in_array($mime, self::ALLOWED_MIMES, true)) {
            throw ValidationException::withMessages([
                'file' => "File MIME type {$mime} is not in the approved allowlist.",
            ]);
        }

        $checksum = hash_file('sha256', $file->getRealPath());

        $width = null;
        $height = null;
        if ($file->getRealPath() && @getimagesize($file->getRealPath()) !== false) {
            $imageInfo = @getimagesize($file->getRealPath());
            if ($imageInfo) {
                $width = $imageInfo[0];
                $height = $imageInfo[1];
            }
        }

        $uuid = (string) Str::uuid();
        $safeFileName = "{$uuid}.{$clientExtension}";
        $storagePath = "media/{$safeFileName}";

        Storage::disk($disk)->putFileAs('media', $file, $safeFileName);

        $isDecorative = ! empty($metadata['is_decorative']);
        $altEn = $isDecorative ? null : ($metadata['alt_text_en'] ?? null);
        $altAr = $isDecorative ? null : ($metadata['alt_text_ar'] ?? null);

        $usageStatus = ($isDecorative || (! empty($altEn) && ! empty($altAr))) ? 'approved' : 'pending';

        return MediaAsset::query()->create([
            'id' => $uuid,
            'disk' => $disk,
            'path' => $storagePath,
            'visibility' => 'public',
            'mime_type' => $mime,
            'size_bytes' => $file->getSize(),
            'original_name' => $originalName,
            'extension' => $clientExtension,
            'checksum' => $checksum,
            'width' => $width,
            'height' => $height,
            'alt_text_en' => $altEn,
            'alt_text_ar' => $altAr,
            'is_decorative' => $isDecorative,
            'caption_en' => $metadata['caption_en'] ?? null,
            'caption_ar' => $metadata['caption_ar'] ?? null,
            'source_owner' => $metadata['source_owner'] ?? null,
            'usage_status' => $usageStatus,
            'focal_x' => isset($metadata['focal_x']) ? (float) $metadata['focal_x'] : 0.5,
            'focal_y' => isset($metadata['focal_y']) ? (float) $metadata['focal_y'] : 0.5,
            'uploaded_by' => $uploader?->getKey(),
        ]);
    }
}
