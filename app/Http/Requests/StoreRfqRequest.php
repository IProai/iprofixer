<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreRfqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $serviceMap = [
            'cutlery' => 'cutlery-restoration',
            'hollowware' => 'hollowware-care',
            'hollowware-restoration' => 'hollowware-care',
            'recurring' => 'recurring-care-plans',
            'maintenance-program' => 'recurring-care-plans',
            'assessment' => 'assessment',
        ];

        $submittedService = (string) $this->input('service_code', $this->input('service'));

        $this->merge([
            'contact_name' => $this->input('contact_name', $this->input('name')),
            'organization_name' => $this->input('organization_name', $this->input('company')),
            'service_code' => $serviceMap[$submittedService] ?? $submittedService,
        ]);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'contact_name' => ['required', 'string', 'max:120'],
            'organization_name' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'service_code' => ['nullable', Rule::in(['cutlery-restoration', 'hollowware-care', 'recurring-care-plans', 'assessment', 'other'])],
            'property_type' => ['nullable', Rule::in(['hotel', 'restaurant', 'catering', 'healthcare', 'education', 'other'])],
            'urgency' => ['nullable', Rule::in(['standard', 'priority', 'urgent'])],
            'estimated_quantity' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'message' => ['nullable', 'string', 'max:5000'],
            'source_page' => ['nullable', 'string', 'max:255'],
            'campaign_source' => ['nullable', 'string', 'max:120'],
            'campaign_medium' => ['nullable', 'string', 'max:120'],
            'campaign_name' => ['nullable', 'string', 'max:160'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
            'consent' => ['accepted'],
            'website' => ['nullable', 'max:0'],
        ];
    }
}
