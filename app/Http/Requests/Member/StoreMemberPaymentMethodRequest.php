<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberPaymentMethodRequest extends FormRequest
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
            'type' => ['required', 'in:card,bank'],
            'brand' => ['required_if:type,card', 'nullable', 'string', 'max:40'],
            'bank_name' => ['required_if:type,bank', 'nullable', 'string', 'max:80'],
            'last4' => ['required', 'digits:4'],
            'exp_month' => ['required_if:type,card', 'nullable', 'integer', 'between:1,12'],
            'exp_year' => ['required_if:type,card', 'nullable', 'integer', 'min:2024', 'max:2100'],
            'holder_name' => ['required', 'string', 'max:120'],
            'is_default' => ['sometimes', 'boolean'],
            'label' => ['nullable', 'string', 'max:80'],
            'card_number' => ['prohibited'],
            'cvv' => ['prohibited'],
            'cvc' => ['prohibited'],
            'account_number' => ['prohibited'],
            'routing_number' => ['prohibited'],
            'iban' => ['prohibited'],
        ];
    }
}
