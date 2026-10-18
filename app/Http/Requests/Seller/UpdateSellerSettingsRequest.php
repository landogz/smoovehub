<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSellerSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'store_name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'city' => ['nullable', 'string', 'max:80'],
            'state' => ['nullable', 'string', 'max:40'],
            'business_type' => ['nullable', 'string', 'max:80'],
            'accepts_onsite_visits' => ['nullable', 'boolean'],
            'policies' => ['nullable', 'array'],
            'policies.returns' => ['nullable', 'string', 'max:4000'],
            'policies.shipping' => ['nullable', 'string', 'max:4000'],
        ];
    }
}
