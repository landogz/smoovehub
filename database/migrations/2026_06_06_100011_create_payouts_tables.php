<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('payable_type');
            $table->unsignedBigInteger('payable_id');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('status')->default('pending');
            $table->string('method')->default('stripe');
            $table->text('stripe_transfer_id')->nullable();
            $table->string('triggered_by')->default('manual');
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['payable_type', 'payable_id']);
        });

        Schema::create('payout_splits', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('payout_id')->constrained()->cascadeOnDelete();
            $table->string('recipient_type');
            $table->unsignedBigInteger('recipient_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->decimal('percentage', 5, 2)->nullable();
            $table->text('stripe_account_id')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_splits');
        Schema::dropIfExists('payouts');
    }
};
