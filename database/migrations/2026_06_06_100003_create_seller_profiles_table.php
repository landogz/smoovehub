<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_profiles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('store_name');
            $table->string('store_slug')->unique();
            $table->text('description')->nullable();
            $table->string('business_type')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            $table->string('country')->default('US');
            $table->text('stripe_connect_id')->nullable();
            $table->timestamp('stripe_onboarded_at')->nullable();
            $table->string('verification_status')->default('pending');
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->unsignedInteger('product_count')->default(0);
            $table->boolean('accepts_onsite_visits')->default(true);
            $table->json('policies')->nullable();
            $table->json('seo_meta')->nullable();
            $table->timestamp('member_since')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_profiles');
    }
};
