<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribution_companies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('owner_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('company_name');
            $table->string('slug')->unique();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('tier')->default('starter');
            $table->string('verification_status')->default('pending');
            $table->text('stripe_connect_id')->nullable();
            $table->decimal('reseller_markup_rate', 5, 4)->default(0.0500);
            $table->json('address')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('distribution_sub_accounts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('distribution_company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('warehouse_location')->nullable();
            $table->text('payout_stripe_account')->nullable();
            $table->boolean('is_payout_enabled')->default(false);
            $table->timestamps();
        });

        Schema::create('distribution_company_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribution_company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('distribution_sub_account_id')->nullable()->constrained()->nullOnDelete();
            $table->string('portal_role')->default('admin');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['distribution_company_id', 'user_id'], 'dist_co_users_company_user_uniq');
        });

        Schema::create('distribution_api_keys', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('distribution_company_id')->constrained()->cascadeOnDelete();
            $table->string('name')->default('primary');
            $table->string('key_prefix', 16);
            $table->string('key_hash');
            $table->json('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_api_keys');
        Schema::dropIfExists('distribution_company_users');
        Schema::dropIfExists('distribution_sub_accounts');
        Schema::dropIfExists('distribution_companies');
    }
};
