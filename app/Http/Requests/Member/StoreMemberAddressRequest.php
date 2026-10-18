<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberAddressRequest extends FormRequest
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
            'label' => ['nullable', 'string', 'max:80'],
            'recipient_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'line1' => ['required', 'string', 'max:160'],
            'line2' => ['nullable', 'string', 'max:160'],
            'city' => ['required', 'string', 'max:80'],
            'state' => ['required', 'string', 'max:80'],
            'zip' => ['required', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:80'],
            'is_default' => ['sometimes', 'boolean'],
            'is_pickup' => ['sometimes', 'boolean'],
            'is_return' => ['sometimes', 'boolean'],
        ];
    }
}
