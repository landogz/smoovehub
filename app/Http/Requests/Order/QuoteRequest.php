<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class QuoteRequest extends FormRequest
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
            'product_id' => ['required', 'uuid'],
            'delivery_type' => ['nullable', 'in:CURBSIDE,IN_HOME'],
        ];
    }
}
