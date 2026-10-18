<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class RedeemMemberVoucherRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:40'],
        ];
    }
}
