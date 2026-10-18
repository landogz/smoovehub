<?php

namespace App\Models\Seller;

use App\Models\Catalog\Product;
use App\Models\Concerns\HasUuid;
use App\Models\Distribution\DistributionCatalogImport;
use App\Models\Member\Voucher;
use App\Models\Tracking\OnsiteVisitAppointment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellerProfile extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'store_name',
        'store_slug',
        'description',
        'business_type',
        'city',
        'state',
        'country',
        'stripe_connect_id',
        'stripe_onboarded_at',
        'verification_status',
        'rating_avg',
        'review_count',
        'product_count',
        'accepts_onsite_visits',
        'policies',
        'seo_meta',
        'member_since',
    ];

    protected function casts(): array
    {
        return [
            'stripe_onboarded_at' => 'datetime',
            'member_since' => 'datetime',
            'rating_avg' => 'decimal:2',
            'accepts_onsite_visits' => 'boolean',
            'policies' => 'array',
            'seo_meta' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function catalogImports(): HasMany
    {
        return $this->hasMany(DistributionCatalogImport::class);
    }

    public function onsiteAppointments(): HasMany
    {
        return $this->hasMany(OnsiteVisitAppointment::class);
    }

    public function payoutAccounts(): HasMany
    {
        return $this->hasMany(SellerPayoutAccount::class);
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }
}
