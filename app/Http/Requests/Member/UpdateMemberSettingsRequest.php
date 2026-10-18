<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberSettingsRequest extends FormRequest
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
            'privacy' => ['sometimes', 'array'],
            'privacy.share_order_activity' => ['sometimes', 'boolean'],
            'privacy.personalized_recommendations' => ['sometimes', 'boolean'],
            'notifications' => ['sometimes', 'array'],
            'notifications.order_updates' => ['sometimes', 'boolean'],
            'notifications.promotions' => ['sometimes', 'boolean'],
            'notifications.wallet' => ['sometimes', 'boolean'],
            'orders' => ['sometimes', 'array'],
            'orders.default_delivery_type' => ['sometimes', 'in:CURBSIDE,IN_HOME'],
            'orders.allow_substitution' => ['sometimes', 'boolean'],
        ];
    }
}
