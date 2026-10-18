<?php

namespace App\Repositories\Member;

use App\Models\Member\MemberAddress;
use App\Models\Member\MemberPaymentMethod;
use App\Models\Member\MemberVoucher;
use App\Models\Member\Voucher;
use App\Models\Order\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MemberAccountRepository
{
    /**
     * @return array<string, mixed>
     */
    public function defaultSettings(): array
    {
        return [
            'privacy' => [
                'share_order_activity' => false,
                'personalized_recommendations' => true,
            ],
            'notifications' => [
                'order_updates' => true,
                'promotions' => true,
                'wallet' => false,
            ],
            'orders' => [
                'default_delivery_type' => 'CURBSIDE',
                'allow_substitution' => false,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function overview(User $user): array
    {
        $orders = $this->ordersQuery($user)->get();
        $unread = $user->unreadNotifications()->count();

        return [
            'member' => $this->profilePayload($user),
            'stats' => [
                'orders' => $orders->count(),
                'unpaid' => $orders->where('payment_status', '!=', 'paid')->count(),
                'unread_notifications' => $unread,
                'vouchers' => MemberVoucher::query()->where('user_id', $user->id)->count(),
            ],
            'settings' => $this->mergedSettings($user),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function profilePayload(User $user): array
    {
        return [
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'email_masked' => $this->maskEmail($user->email),
            'phone' => $user->phone,
            'phone_masked' => $this->maskPhone($user->phone),
            'gender' => $user->gender,
            'date_of_birth' => $user->date_of_birth?->format('Y-m-d'),
            'avatar_url' => $this->avatarUrl($user),
            'initials' => strtoupper(substr((string) $user->name, 0, 1)),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function updateProfile(User $user, array $payload): array
    {
        $user->fill([
            'name' => $payload['name'],
            'username' => $payload['username'],
            'gender' => $payload['gender'] ?? $user->gender,
            'date_of_birth' => $payload['date_of_birth'] ?? $user->date_of_birth,
        ]);

        if (array_key_exists('email', $payload) && filled($payload['email'])) {
            $user->email = $payload['email'];
        }

        if (array_key_exists('phone', $payload)) {
            $user->phone = $payload['phone'] ?: null;
        }

        $user->save();

        return $this->profilePayload($user->fresh());
    }

    public function updatePassword(User $user, string $password): void
    {
        $user->password = $password;
        $user->save();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function updateSettings(User $user, array $payload): array
    {
        $merged = array_replace_recursive($this->mergedSettings($user), $payload);
        $user->member_settings = $merged;
        $user->save();

        return $this->mergedSettings($user->fresh());
    }

    public function storeAvatar(User $user, string $path): string
    {
        if ($user->avatar_path && $user->avatar_path !== $path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->avatar_path = $path;
        $user->save();

        return $this->avatarUrl($user) ?? '';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function addresses(User $user): array
    {
        return MemberAddress::query()
            ->where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->latest()
            ->get()
            ->map(fn (MemberAddress $address) => $this->addressPayload($address))
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function storeAddress(User $user, array $payload): array
    {
        return DB::transaction(function () use ($user, $payload) {
            $address = MemberAddress::query()->create([
                ...$payload,
                'user_id' => $user->id,
                'country' => $payload['country'] ?? 'US',
                'is_default' => (bool) ($payload['is_default'] ?? false),
                'is_pickup' => (bool) ($payload['is_pickup'] ?? false),
                'is_return' => (bool) ($payload['is_return'] ?? false),
            ]);

            if ($address->is_default) {
                $this->clearOtherDefaultAddresses($user, $address->id);
            }

            return $this->addressPayload($address->fresh());
        });
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function updateAddress(User $user, string $uuid, array $payload): array
    {
        return DB::transaction(function () use ($user, $uuid, $payload) {
            $address = $this->requireAddress($user, $uuid);
            $address->fill($payload);
            $address->save();

            if ($address->is_default) {
                $this->clearOtherDefaultAddresses($user, $address->id);
            }

            return $this->addressPayload($address->fresh());
        });
    }

    public function deleteAddress(User $user, string $uuid): void
    {
        $this->requireAddress($user, $uuid)->delete();
    }

    /**
     * @return array<string, mixed>
     */
    public function setDefaultAddress(User $user, string $uuid): array
    {
        return DB::transaction(function () use ($user, $uuid) {
            $address = $this->requireAddress($user, $uuid);
            $this->clearOtherDefaultAddresses($user, $address->id);
            $address->is_default = true;
            $address->save();

            return $this->addressPayload($address);
        });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function paymentMethods(User $user): array
    {
        return MemberPaymentMethod::query()
            ->where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->latest()
            ->get()
            ->map(fn (MemberPaymentMethod $method) => $this->paymentPayload($method))
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function storePaymentMethod(User $user, array $payload): array
    {
        return DB::transaction(function () use ($user, $payload) {
            $method = MemberPaymentMethod::query()->create([
                'user_id' => $user->id,
                'type' => $payload['type'],
                'brand' => $payload['brand'] ?? null,
                'bank_name' => $payload['bank_name'] ?? null,
                'last4' => $payload['last4'],
                'exp_month' => $payload['exp_month'] ?? null,
                'exp_year' => $payload['exp_year'] ?? null,
                'holder_name' => $payload['holder_name'],
                'is_default' => (bool) ($payload['is_default'] ?? false),
                'encrypted_meta' => encrypt([
                    'label' => $payload['label'] ?? null,
                ]),
            ]);

            if ($method->is_default) {
                $this->clearOtherDefaultPayments($user, $method->id);
            }

            return $this->paymentPayload($method);
        });
    }

    public function deletePaymentMethod(User $user, string $uuid): void
    {
        $this->requirePaymentMethod($user, $uuid)->delete();
    }

    /**
     * @return array<string, mixed>
     */
    public function setDefaultPaymentMethod(User $user, string $uuid): array
    {
        return DB::transaction(function () use ($user, $uuid) {
            $method = $this->requirePaymentMethod($user, $uuid);
            $this->clearOtherDefaultPayments($user, $method->id);
            $method->is_default = true;
            $method->save();

            return $this->paymentPayload($method);
        });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function orders(User $user, string $tab, string $query): array
    {
        $builder = $this->ordersQuery($user)->with(['items', 'sellerProfile']);

        match ($tab) {
            'to_pay' => $builder->where('payment_status', '!=', 'paid')->whereNotIn('status', ['cancelled', 'refunded']),
            'to_ship' => $builder->where('payment_status', 'paid')->whereIn('status', ['placed', 'processing', 'pending']),
            'to_receive' => $builder->whereIn('status', ['in_transit', 'shipped']),
            'completed' => $builder->whereIn('status', ['delivered', 'completed']),
            'cancelled' => $builder->where('status', 'cancelled'),
            'return_refund' => $builder->whereIn('status', ['refunded', 'returned']),
            default => $builder,
        };

        if ($query !== '') {
            $like = '%'.$query.'%';
            $builder->where(function ($inner) use ($like): void {
                $inner->where('order_number', 'like', $like)
                    ->orWhereHas('items', fn ($items) => $items->where('name', 'like', $like)->orWhere('seller_name', 'like', $like))
                    ->orWhereHas('sellerProfile', fn ($seller) => $seller->where('store_name', 'like', $like));
            });
        }

        return $builder->get()
            ->map(fn (Order $order) => $this->orderPayload($order))
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function notifications(User $user): array
    {
        return $user->notifications()
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn ($notification) => [
                'id' => $notification->id,
                'title' => $notification->data['title'] ?? 'Update',
                'body' => $notification->data['body'] ?? '',
                'order_number' => $notification->data['order_number'] ?? null,
                'created_at' => $notification->created_at?->timezone(config('app.timezone'))->format('M j, Y H:i'),
                'read' => $notification->read_at !== null,
            ])
            ->values()
            ->all();
    }

    public function markNotificationRead(User $user, string $id): void
    {
        $notification = $user->notifications()->where('id', $id)->first();
        $notification?->markAsRead();
    }

    public function markAllNotificationsRead(User $user): void
    {
        $user->unreadNotifications->markAsRead();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function vouchers(User $user): array
    {
        return MemberVoucher::query()
            ->where('user_id', $user->id)
            ->with('voucher')
            ->latest()
            ->get()
            ->map(fn (MemberVoucher $row) => $this->voucherWalletPayload($row))
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function redeemVoucher(User $user, string $code): array
    {
        $voucher = Voucher::query()
            ->whereRaw('UPPER(code) = ?', [strtoupper(trim($code))])
            ->first();

        if (! $voucher || $voucher->status !== 'active' || $voucher->isExpired()) {
            throw ValidationException::withMessages([
                'code' => ['This voucher code is invalid or expired.'],
            ]);
        }

        $existing = MemberVoucher::query()
            ->where('user_id', $user->id)
            ->where('voucher_id', $voucher->id)
            ->first();

        if ($existing) {
            throw ValidationException::withMessages([
                'code' => ['You already added this voucher.'],
            ]);
        }

        $row = MemberVoucher::query()->create([
            'user_id' => $user->id,
            'voucher_id' => $voucher->id,
        ]);

        return $this->voucherWalletPayload($row->load('voucher'));
    }

    /**
     * @return array<string, mixed>
     */
    private function mergedSettings(User $user): array
    {
        return array_replace_recursive($this->defaultSettings(), $user->member_settings ?? []);
    }

    private function ordersQuery(User $user)
    {
        return Order::query()->where('buyer_id', $user->id)->latest();
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
            'seller' => $order->sellerProfile?->store_name ?? $item?->seller_name ?? 'SmooveHub',
            'product' => $item?->name ?? 'Order',
            'quantity' => (int) ($item?->quantity ?? 1),
            'total' => (float) $order->total,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'tab_status' => $this->purchaseTabLabel($order),
            'date' => $order->created_at?->timezone(config('app.timezone'))->format('M j, Y'),
            'delivery_zip' => is_array($notes) ? ($notes['delivery_zip'] ?? null) : null,
        ];
    }

    private function purchaseTabLabel(Order $order): string
    {
        if ($order->status === 'cancelled') {
            return 'Cancelled';
        }

        if (in_array($order->status, ['refunded', 'returned'], true)) {
            return 'Return / Refund';
        }

        if (in_array($order->status, ['delivered', 'completed'], true)) {
            return 'Completed';
        }

        if (in_array($order->status, ['in_transit', 'shipped'], true)) {
            return 'To Receive';
        }

        if ($order->payment_status !== 'paid') {
            return 'To Pay';
        }

        return 'To Ship';
    }

    /**
     * @return array<string, mixed>
     */
    private function addressPayload(MemberAddress $address): array
    {
        return [
            'id' => $address->uuid,
            'label' => $address->label,
            'recipient_name' => $address->recipient_name,
            'phone' => $address->phone,
            'line1' => $address->line1,
            'line2' => $address->line2,
            'city' => $address->city,
            'state' => $address->state,
            'zip' => $address->zip,
            'country' => $address->country,
            'is_default' => $address->is_default,
            'is_pickup' => $address->is_pickup,
            'is_return' => $address->is_return,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function paymentPayload(MemberPaymentMethod $method): array
    {
        return [
            'id' => $method->uuid,
            'type' => $method->type,
            'brand' => $method->brand,
            'bank_name' => $method->bank_name,
            'last4' => $method->last4,
            'exp_month' => $method->exp_month,
            'exp_year' => $method->exp_year,
            'holder_name' => $method->holder_name,
            'is_default' => $method->is_default,
            'masked' => '**** **** **** '.$method->last4,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function voucherWalletPayload(MemberVoucher $row): array
    {
        $voucher = $row->voucher;

        return [
            'id' => $row->uuid,
            'code' => $voucher?->code,
            'title' => $voucher?->title,
            'type' => $voucher?->type,
            'value' => $voucher ? (float) $voucher->value : 0,
            'min_spend' => $voucher ? (float) $voucher->min_spend : 0,
            'expires_at' => $voucher?->expires_at?->timezone(config('app.timezone'))->format('M j, Y'),
            'expired' => $voucher?->isExpired() ?? false,
        ];
    }

    private function requireAddress(User $user, string $uuid): MemberAddress
    {
        $address = MemberAddress::query()
            ->where('user_id', $user->id)
            ->where('uuid', $uuid)
            ->first();

        if (! $address) {
            throw ValidationException::withMessages([
                'id' => ['Address not found.'],
            ]);
        }

        return $address;
    }

    private function requirePaymentMethod(User $user, string $uuid): MemberPaymentMethod
    {
        $method = MemberPaymentMethod::query()
            ->where('user_id', $user->id)
            ->where('uuid', $uuid)
            ->first();

        if (! $method) {
            throw ValidationException::withMessages([
                'id' => ['Payment method not found.'],
            ]);
        }

        return $method;
    }

    private function clearOtherDefaultAddresses(User $user, int $keepId): void
    {
        MemberAddress::query()
            ->where('user_id', $user->id)
            ->where('id', '!=', $keepId)
            ->update(['is_default' => false]);
    }

    private function clearOtherDefaultPayments(User $user, int $keepId): void
    {
        MemberPaymentMethod::query()
            ->where('user_id', $user->id)
            ->where('id', '!=', $keepId)
            ->update(['is_default' => false]);
    }

    private function avatarUrl(User $user): ?string
    {
        if (! $user->avatar_path) {
            return null;
        }

        return Storage::disk('public')->url($user->avatar_path);
    }

    private function maskEmail(?string $email): ?string
    {
        if (! $email || ! str_contains($email, '@')) {
            return $email;
        }

        [$local, $domain] = explode('@', $email, 2);
        $visible = substr($local, 0, 2);

        return $visible.str_repeat('*', max(3, strlen($local) - 2)).'@'.$domain;
    }

    private function maskPhone(?string $phone): ?string
    {
        if (! $phone) {
            return $phone;
        }

        $digits = preg_replace('/\D+/', '', $phone) ?: '';

        if (strlen($digits) < 4) {
            return str_repeat('*', strlen($phone));
        }

        return str_repeat('*', max(4, strlen($digits) - 4)).substr($digits, -4);
    }
}
