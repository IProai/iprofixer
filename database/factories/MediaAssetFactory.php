<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MediaAsset;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MediaAsset>
 */
final class MediaAssetFactory extends Factory
{
    protected $model = MediaAsset::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'disk' => 'public',
            'path' => 'media/'.Str::random(20).'.jpg',
            'visibility' => 'public',
            'mime_type' => 'image/jpeg',
            'size_bytes' => 102400,
            'original_name' => 'hospitality-table.jpg',
            'extension' => 'jpg',
            'checksum' => hash('sha256', 'dummy-content'),
            'width' => 1200,
            'height' => 800,
            'alt_text_en' => 'Restored silver cutlery set on dining table',
            'alt_text_ar' => 'طقم أدوات مائدة فضية مرممة على طاول الطعام',
            'is_decorative' => false,
            'caption_en' => 'Hospitality dining setup',
            'caption_ar' => 'تجهيز طعام الضيافة',
            'source_owner' => 'IProFixer Studio',
            'usage_status' => 'approved',
            'focal_x' => 0.5,
            'focal_y' => 0.5,
        ];
    }
}
