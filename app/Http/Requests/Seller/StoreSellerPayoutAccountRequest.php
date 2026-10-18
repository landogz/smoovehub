<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class StoreSellerPayoutAccountRequest extends FormRequest
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
            'bank_name' => ['required', 'string', 'max:120'],
            'holder_name' => ['required', 'string', 'max:120'],
            'account_type' => ['required', 'in:checking,savings'],
            'last4' => ['required', 'digits:4'],
            'is_default' => ['nullable', 'boolean'],
            'account_number' => ['prohibited'],
            'routing_number' => ['prohibited'],
        ];
    }
}
