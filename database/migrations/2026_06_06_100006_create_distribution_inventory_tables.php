<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribution_inventory', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('distribution_company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('distribution_sub_account_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sku');
            $table->string('upc')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('brand')->nullable();
            $table->string('material')->nullable();
            $table->string('color')->nullable();
            $table->string('dimensions')->nullable();
            $table->decimal('weight_lbs', 10, 2)->nullable();
            $table->text('supplier_price')->nullable();
            $table->decimal('internal_reseller_price', 12, 2)->default(0);
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(5);
            $table->string('warehouse_location')->nullable();
            $table->date('eta')->nullable();
            $table->boolean('assembly_required')->default(false);
            $table->string('warranty')->nullable();
            $table->string('condition')->default('BRAND_NEW');
            $table->string('status')->default('active');
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['distribution_company_id', 'sku'], 'dist_inventory_company_sku_uniq');
        });

        Schema::create('inventory_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('distribution_company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('distribution_inventory_id')->nullable()->constrained('distribution_inventory')->nullOnDelete();
            $table->string('source')->default('api');
            $table->string('event');
            $table->integer('qty_change')->default(0);
            $table->json('payload')->nullable();
            $table->string('status')->default('processed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_sync_logs');
        Schema::dropIfExists('distribution_inventory');
    }
};
