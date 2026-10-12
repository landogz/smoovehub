<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_quote_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->string('carrier')->default('crs');
            $table->text('external_job_id')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('estimated_delivery_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->string('proof_url')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('delivery_timeline_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('delivery_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('step');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('occurred_at');
            $table->json('location')->nullable();
            $table->timestamps();
        });

        Schema::create('waybills', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_id')->nullable()->constrained()->nullOnDelete();
            $table->string('waybill_number')->unique();
            $table->text('barcode_data')->nullable();
            $table->longText('html_content')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamp('generated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waybills');
        Schema::dropIfExists('delivery_timeline_events');
        Schema::dropIfExists('deliveries');
    }
};
