<?php

namespace App\Http\Requests\Seller;

use App\Models\Member\Voucher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSellerVoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge(['code' => strtoupper(trim((string) $this->input('code')))]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $uuid = (string) $this->route('uuid');
        $ignoreId = Voucher::query()->where('uuid', $uuid)->value('id');

        return [
            'code' => ['required', 'string', 'max:40', 'alpha_dash', Rule::unique('vouchers', 'code')->ignore($ignoreId)],
            'title' => ['required', 'string', 'max:120'],
            'type' => ['required', 'in:amount,percent'],
            'value' => ['required', 'numeric', 'min:0.01'],
            'min_spend' => ['nullable', 'numeric', 'min:0'],
            'expires_at' => ['nullable', 'date'],
            'status' => ['nullable', 'in:active,inactive'],
        ];
    }
}
