<?php

namespace App\Models\Distribution;

use App\Models\Catalog\Category;
use App\Models\Catalog\Product;
use App\Models\Catalog\ProductImage;
use App\Models\Concerns\HasUuid;
use App\Models\Tracking\InventoryQrCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DistributionInventory extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'distribution_inventory';

    protected $fillable = [
        'uuid',
        'distribution_company_id',
        'distribution_sub_account_id',
        'category_id',
        'sku',
        'upc',
        'name',
        'description',
        'brand',
        'material',
        'color',
        'dimensions',
        'weight_lbs',
        'supplier_price',
        'internal_reseller_price',
        'quantity',
        'low_stock_threshold',
        'warehouse_location',
        'eta',
        'assembly_required',
        'warranty',
        'condition',
        'status',
        'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'weight_lbs' => 'decimal:2',
            'internal_reseller_price' => 'decimal:2',
            'eta' => 'date',
            'assembly_required' => 'boolean',
            'last_synced_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(DistributionCompany::class, 'distribution_company_id');
    }

    public function subAccount(): BelongsTo
    {
        return $this->belongsTo(DistributionSubAccount::class, 'distribution_sub_account_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function syncLogs(): HasMany
    {
        return $this->hasMany(InventorySyncLog::class);
    }

    public function qrCodes(): HasMany
    {
        return $this->hasMany(InventoryQrCode::class);
    }

    public function catalogImports(): HasMany
    {
        return $this->hasMany(DistributionCatalogImport::class);
    }
}
