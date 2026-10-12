<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_qr_codes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('token')->unique();
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('distribution_inventory_id')->nullable()->constrained('distribution_inventory')->cascadeOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('qr_image_path')->nullable();
            $table->string('scan_url');
            $table->unsignedInteger('scan_count')->default(0);
            $table->timestamp('last_scanned_at')->nullable();
            $table->timestamps();
        });

        Schema::create('onsite_visit_appointments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_profile_id')->constrained()->cascadeOnDelete();
            $table->timestamp('scheduled_at');
            $table->string('status')->default('requested');
            $table->text('customer_notes')->nullable();
            $table->text('seller_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onsite_visit_appointments');
        Schema::dropIfExists('inventory_qr_codes');
    }
};
