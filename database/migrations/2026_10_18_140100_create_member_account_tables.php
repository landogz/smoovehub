<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_addresses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label')->nullable();
            $table->string('recipient_name');
            $table->string('phone', 30);
            $table->string('line1');
            $table->string('line2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('zip', 20);
            $table->string('country')->default('US');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_pickup')->default(false);
            $table->boolean('is_return')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_default']);
        });

        Schema::create('member_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('brand')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('last4', 4);
            $table->unsignedTinyInteger('exp_month')->nullable();
            $table->unsignedSmallInteger('exp_year')->nullable();
            $table->string('holder_name');
            $table->boolean('is_default')->default(false);
            $table->text('encrypted_meta')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type']);
        });

        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code')->unique();
            $table->string('title');
            $table->string('type');
            $table->decimal('value', 12, 2);
            $table->decimal('min_spend', 12, 2)->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('member_vouchers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('voucher_id')->constrained()->cascadeOnDelete();
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'voucher_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_vouchers');
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('member_payment_methods');
        Schema::dropIfExists('member_addresses');
    }
};
