<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\NavigationItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreNavigationItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        return $user->can('navigation.edit') || $user->can('content.manage');
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'navigation_menu_id' => ['required', 'exists:navigation_menus,id'],
            'parent_id' => ['nullable', 'exists:navigation_items,id'],
            'label_en' => ['required', 'string', 'max:120'],
            'label_ar' => ['required', 'string', 'max:120'],
            'destination_type' => ['required', Rule::in(['internal_route', 'content_page', 'external_url'])],
            'route_name' => ['nullable', 'required_if:destination_type,internal_route', 'string', 'max:120'],
            'content_page_id' => ['nullable', 'required_if:destination_type,content_page', 'exists:content_pages,id'],
            'url' => [
                'nullable',
                'required_if:destination_type,external_url',
                'string',
                'max:500',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value && ! NavigationItem::isValidUrl((string) $value)) {
                        $fail('Unsafe or invalid URL protocol specified. Prohibited protocols include javascript:, data:, and file:');
                    }
                },
            ],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'target_blank' => ['nullable', 'boolean'],
        ];
    }
}
