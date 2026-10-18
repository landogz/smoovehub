<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->foreignId('seller_profile_id')->nullable()->after('uuid')->constrained()->nullOnDelete();
            $table->index('seller_profile_id');
        });

        Schema::create('seller_payout_accounts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('seller_profile_id')->constrained()->cascadeOnDelete();
            $table->string('bank_name');
            $table->string('holder_name');
            $table->string('account_type')->default('checking');
            $table->string('last4', 4);
            $table->boolean('is_default')->default(false);
            $table->text('encrypted_meta')->nullable();
            $table->timestamps();

            $table->index(['seller_profile_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_payout_accounts');

        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('seller_profile_id');
        });
    }
};
