<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:120'],
            'device_fingerprint' => ['nullable', 'string', 'max:120'],
            'platform' => ['nullable', 'string', 'max:80'],
            'browser' => ['nullable', 'string', 'max:80'],
        ];
    }
}
