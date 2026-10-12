<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class StoreSellerProductRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'base_price' => ['required', 'numeric', 'min:1'],
            'condition' => ['required', 'in:BRAND_NEW,THRIFT,FLOOR_MODEL,REFURBISHED'],
            'category' => ['required', 'in:COMMERCIAL,RESIDENTIAL,APPLIANCES'],
            'quantity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:5000'],
            'same_day_delivery' => ['nullable', 'boolean'],
        ];
    }
}
