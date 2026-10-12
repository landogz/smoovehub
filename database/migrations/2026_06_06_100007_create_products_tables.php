<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('seller_profile_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('distribution_inventory_id')->nullable()->constrained('distribution_inventory')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sku')->nullable();
            $table->string('upc')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('condition')->default('BRAND_NEW');
            $table->string('brand')->nullable();
            $table->string('material')->nullable();
            $table->string('color')->nullable();
            $table->string('dimensions')->nullable();
            $table->decimal('weight_lbs', 10, 2)->nullable();
            $table->text('supplier_price')->nullable();
            $table->decimal('internal_price', 12, 2)->nullable();
            $table->decimal('listing_price', 12, 2);
            $table->decimal('compare_at_price', 12, 2)->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('warehouse_location')->nullable();
            $table->boolean('assembly_required')->default(false);
            $table->string('warranty')->nullable();
            $table->boolean('same_day_delivery')->default(false);
            $table->boolean('is_trending')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->string('ownership_type')->default('seller');
            $table->string('status')->default('active');
            $table->json('seo_meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('distribution_inventory_id')->nullable()->constrained('distribution_inventory')->cascadeOnDelete();
            $table->string('disk')->default('local');
            $table->string('path');
            $table->string('alt_text')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        Schema::create('distribution_catalog_imports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('seller_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('distribution_company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('distribution_inventory_id')->constrained('distribution_inventory')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('wholesale_price', 12, 2);
            $table->decimal('resale_price', 12, 2)->nullable();
            $table->string('status')->default('imported');
            $table->timestamp('imported_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_catalog_imports');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
    }
};
