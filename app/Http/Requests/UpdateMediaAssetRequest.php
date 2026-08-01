<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateMediaAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        return $user->can('media.edit') || $user->can('content.manage');
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'is_decorative' => ['nullable', 'boolean'],
            'alt_text_en' => ['nullable', 'string', 'max:255'],
            'alt_text_ar' => ['nullable', 'string', 'max:255'],
            'caption_en' => ['nullable', 'string', 'max:500'],
            'caption_ar' => ['nullable', 'string', 'max:500'],
            'source_owner' => ['nullable', 'string', 'max:160'],
            'focal_x' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'focal_y' => ['nullable', 'numeric', 'min:0', 'max:1'],
        ];
    }
}
