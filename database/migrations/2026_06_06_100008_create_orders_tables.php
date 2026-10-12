<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_quotes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('distribution_company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('quote_number')->unique();
            $table->string('contact_name');
            $table->string('contact_phone');
            $table->string('contact_email');
            $table->json('pickup_address');
            $table->json('dropoff_address');
            $table->json('items_description')->nullable();
            $table->json('access_details')->nullable();
            $table->decimal('distance_miles', 8, 2)->nullable();
            $table->string('distance_tier')->nullable();
            $table->string('crs_quote_id')->nullable();
            $table->decimal('estimated_fee', 12, 2)->nullable();
            $table->decimal('stairs_surcharge', 12, 2)->default(0);
            $table->decimal('elevator_surcharge', 12, 2)->default(0);
            $table->decimal('long_carry_surcharge', 12, 2)->default(0);
            $table->decimal('waiting_time_surcharge', 12, 2)->default(0);
            $table->string('status')->default('draft');
            $table->string('source')->default('public');
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('order_number')->unique();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_profile_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('distribution_company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('delivery_quote_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_type')->default('customer');
            $table->string('fulfillment_source')->default('seller');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->decimal('processing_fee', 12, 2)->default(0);
            $table->decimal('platform_fee', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->string('status')->default('pending');
            $table->string('payment_status')->default('unpaid');
            $table->text('stripe_payment_intent_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('distribution_inventory_id')->nullable()->constrained('distribution_inventory')->nullOnDelete();
            $table->string('sku')->nullable();
            $table->string('name');
            $table->string('condition')->nullable();
            $table->string('seller_name')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('line_total', 12, 2);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('delivery_quotes');
    }
};
