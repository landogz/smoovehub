<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_rooms', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('room_type')->nullable();
            $table->json('dimensions')->nullable();
            $table->json('canvas_data')->nullable();
            $table->string('photo_path')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });

        Schema::create('design_room_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->json('position')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('custom_furniture_designs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('furniture_type');
            $table->string('material')->nullable();
            $table->string('color')->nullable();
            $table->json('dimensions')->nullable();
            $table->json('features')->nullable();
            $table->string('sketch_path')->nullable();
            $table->string('reference_image_path')->nullable();
            $table->decimal('estimated_price', 12, 2)->nullable();
            $table->unsignedSmallInteger('build_timeline_days')->nullable();
            $table->string('status')->default('submitted');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_furniture_designs');
        Schema::dropIfExists('design_room_items');
        Schema::dropIfExists('design_rooms');
    }
};
