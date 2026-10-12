<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class SellerRegistrationRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'store_name' => ['required', 'string', 'max:255'],
            'business_type' => ['required', 'string', 'max:120'],
            'category' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:2000'],
            'catalog_size' => ['nullable', 'string', 'max:80'],
            'tax_id' => ['nullable', 'string', 'max:40'],
            'device_name' => ['nullable', 'string', 'max:120'],
            'device_fingerprint' => ['nullable', 'string', 'max:120'],
            'platform' => ['nullable', 'string', 'max:80'],
            'browser' => ['nullable', 'string', 'max:80'],
        ];
    }
}
