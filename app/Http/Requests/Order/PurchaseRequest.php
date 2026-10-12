<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
            'product_id' => ['required'],
            'product_name' => ['required', 'string', 'max:255'],
            'seller_name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'delivery_address' => ['required', 'string', 'max:500'],
            'delivery_zip' => ['required', 'string', 'max:10'],
            'delivery_type' => ['nullable', 'in:CURBSIDE,IN_HOME'],
            'total' => ['required', 'numeric', 'min:0'],
        ];
    }
}
