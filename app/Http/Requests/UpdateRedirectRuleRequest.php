<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\RedirectRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateRedirectRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        return $user->can('redirects.edit') || $user->can('content.manage');
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var RedirectRule|null $redirect */
        $redirect = $this->route('redirect') ?? $this->route('redirectRule');

        return [
            'source_path' => [
                'required',
                'string',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail) use ($redirect): void {
                    $normalized = RedirectRule::normalizePath((string) $value);
                    if (RedirectRule::isProtectedPath($normalized)) {
                        $fail('Redirect source path cannot override protected system routes like /admin, /portal, /api, or /login.');
                    }
                    $exists = RedirectRule::query()
                        ->where('source_path', $normalized)
                        ->when($redirect, fn ($q) => $q->where('id', '!=', $redirect->id))
                        ->exists();

                    if ($exists) {
                        $fail('Another redirect rule already exists for this source path.');
                    }
                },
            ],
            'destination_type' => ['required', Rule::in(['internal_route', 'content_page', 'custom_url'])],
            'destination_path' => [
                'required',
                'string',
                'max:500',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $dest = (string) $value;
                    if (! RedirectRule::isValidDestination($dest)) {
                        $fail('Unsafe destination URL protocol specified. Prohibited protocols include javascript:, data:, and file:');
                    }
                    $sourceNorm = RedirectRule::normalizePath((string) $this->input('source_path'));
                    $destNorm = RedirectRule::normalizePath($dest);
                    if ($sourceNorm === $destNorm) {
                        $fail('Redirect source path and destination path cannot be identical (direct loop).');
                    }
                },
            ],
            'status_code' => ['required', Rule::in([301, 302])],
            'is_active' => ['nullable', 'boolean'],
            'locale' => ['nullable', 'string', 'max:5'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
