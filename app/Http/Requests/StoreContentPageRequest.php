<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreContentPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        return $user->can('content.create')
            || $user->can('content.edit')
            || $user->can('content.manage');
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'slug' => ['required', 'string', 'max:160', 'alpha_dash', Rule::unique('content_pages', 'slug')],
            'type' => ['required', Rule::in(['page', 'service', 'industry', 'resource'])],
            'status' => ['required', Rule::in(['draft', 'review', 'approved', 'scheduled', 'published'])],
            'scheduled_for' => ['nullable', 'date'],
            'approve_en' => ['nullable', 'boolean'],
            'approve_ar' => ['nullable', 'boolean'],
            'title_en' => ['required', 'string', 'max:180'],
            'title_ar' => ['required', 'string', 'max:180'],
            'summary_en' => ['nullable', 'string', 'max:500'],
            'summary_ar' => ['nullable', 'string', 'max:500'],
            'body_en' => ['required', 'string'],
            'body_ar' => ['required', 'string'],
            'seo_title_en' => ['nullable', 'string', 'max:70'],
            'seo_title_ar' => ['nullable', 'string', 'max:70'],
            'seo_description_en' => ['nullable', 'string', 'max:170'],
            'seo_description_ar' => ['nullable', 'string', 'max:170'],
        ];
    }
}
