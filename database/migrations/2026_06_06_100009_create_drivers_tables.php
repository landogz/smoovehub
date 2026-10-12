<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->text('mc_dot_number')->nullable();
            $table->text('insurance_policy')->nullable();
            $table->text('cdl_number')->nullable();
            $table->string('verification_status')->default('pending');
            $table->boolean('crs_crm_linked')->default(false);
            $table->unsignedInteger('delivery_count')->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('driver_documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('driver_id')->constrained()->cascadeOnDelete();
            $table->string('document_type');
            $table->string('disk')->default('local');
            $table->string('path');
            $table->string('verification_status')->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('driver_payout_methods', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('driver_id')->constrained()->cascadeOnDelete();
            $table->string('method');
            $table->text('account_details')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_payout_methods');
        Schema::dropIfExists('driver_documents');
        Schema::dropIfExists('drivers');
    }
};
