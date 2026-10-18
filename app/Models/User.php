<?php

namespace App\Models;

use App\Models\Auth\UserDevice;
use App\Models\Concerns\HasUuid;
use App\Models\Design\CustomFurnitureDesign;
use App\Models\Design\DesignRoom;
use App\Models\Distribution\DistributionCompany;
use App\Models\Distribution\DistributionCompanyUser;
use App\Models\Member\MemberAddress;
use App\Models\Member\MemberPaymentMethod;
use App\Models\Member\MemberVoucher;
use App\Models\Order\DeliveryQuote;
use App\Models\Order\Order;
use App\Models\Seller\SellerProfile;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'uuid',
    'name',
    'username',
    'email',
    'phone',
    'gender',
    'date_of_birth',
    'password',
    'status',
    'last_login_at',
    'avatar_path',
    'member_settings',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasUuid, Notifiable, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'date_of_birth' => 'date',
            'member_settings' => 'array',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function sellerProfile(): HasOne
    {
        return $this->hasOne(SellerProfile::class);
    }

    public function ownedDistributionCompanies(): HasMany
    {
        return $this->hasMany(DistributionCompany::class, 'owner_user_id');
    }

    public function distributionMemberships(): HasMany
    {
        return $this->hasMany(DistributionCompanyUser::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(UserDevice::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function memberAddresses(): HasMany
    {
        return $this->hasMany(MemberAddress::class);
    }

    public function memberPaymentMethods(): HasMany
    {
        return $this->hasMany(MemberPaymentMethod::class);
    }

    public function memberVouchers(): HasMany
    {
        return $this->hasMany(MemberVoucher::class);
    }

    public function deliveryQuotes(): HasMany
    {
        return $this->hasMany(DeliveryQuote::class);
    }

    public function designRooms(): HasMany
    {
        return $this->hasMany(DesignRoom::class);
    }

    public function customFurnitureDesigns(): HasMany
    {
        return $this->hasMany(CustomFurnitureDesign::class);
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles()->where('slug', $slug)->exists();
    }

    public function assignRole(string $slug): void
    {
        $role = Role::query()->where('slug', $slug)->first();

        if ($role && ! $this->hasRole($slug)) {
            $this->roles()->attach($role);
        }
    }
}
