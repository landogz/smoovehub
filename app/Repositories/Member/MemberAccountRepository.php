<?php

namespace App\Repositories\Member;

use App\Models\Order\Order;
use App\Models\User;

class MemberAccountRepository
{
    /**
     * @return array<string, mixed>
     */
    public function overview(User $user): array
    {
        $orders = Order::query()
            ->where('buyer_id', $user->id)
            ->with('items')
            ->latest()
            ->get();

        return [
            'member' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'stats' => [
                'orders' => $orders->count(),
                'unpaid' => $orders->where('payment_status', '!=', 'paid')->count(),
            ],
            'orders' => $orders->map(fn (Order $order) => $this->orderPayload($order))->values()->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function orderPayload(Order $order): array
    {
        $item = $order->items->first();
        $notes = is_string($order->notes) ? json_decode($order->notes, true) : [];

        return [
            'id' => $order->uuid,
            'order_number' => $order->order_number,
            'product' => $item?->name ?? 'Order',
            'total' => (float) $order->total,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'date' => $order->created_at?->timezone(config('app.timezone'))->format('M j, Y'),
            'delivery_zip' => is_array($notes) ? ($notes['delivery_zip'] ?? null) : null,
        ];
    }
}
