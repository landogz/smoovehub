<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('markup_rules', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('rule_key')->unique();
            $table->decimal('rate', 8, 4);
            $table->string('applies_to')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('effective_from')->nullable();
            $table->timestamps();
        });

        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('general');
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->boolean('is_encrypted')->default(false);
            $table->timestamps();
        });

        Schema::create('platform_files', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('owner_type');
            $table->unsignedBigInteger('owner_id');
            $table->string('name');
            $table->string('disk')->default('local');
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('category')->default('document');
            $table->timestamps();

            $table->index(['owner_type', 'owner_id']);
        });

        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('owner_type');
            $table->unsignedBigInteger('owner_id');
            $table->string('url');
            $table->json('events')->nullable();
            $table->text('secret')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['owner_type', 'owner_id']);
        });

        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('webhook_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event');
            $table->json('payload')->nullable();
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('carrier_integrations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('carrier')->unique();
            $table->string('display_name');
            $table->json('config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carrier_integrations');
        Schema::dropIfExists('webhook_logs');
        Schema::dropIfExists('webhooks');
        Schema::dropIfExists('platform_files');
        Schema::dropIfExists('platform_settings');
        Schema::dropIfExists('markup_rules');
    }
};
