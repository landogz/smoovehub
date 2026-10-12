<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_me_orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('furniture_type');
            $table->string('material')->nullable();
            $table->string('color')->nullable();
            $table->string('color_name')->nullable();
            $table->string('finish')->nullable();
            $table->json('dimensions')->nullable();
            $table->json('features')->nullable();
            $table->string('timeline')->default('standard');
            $table->string('delivery_method')->default('standard');
            $table->text('contact_name')->nullable();
            $table->text('contact_email')->nullable();
            $table->text('contact_phone')->nullable();
            $table->text('contact_address')->nullable();
            $table->string('source')->nullable();
            $table->text('notes')->nullable();
            $table->string('sketch_path')->nullable();
            $table->json('reference_images')->nullable();
            $table->decimal('estimated_price', 12, 2)->nullable();
            $table->decimal('deposit_amount', 12, 2)->nullable();
            $table->string('status')->default('submitted');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_me_orders');
    }
};
