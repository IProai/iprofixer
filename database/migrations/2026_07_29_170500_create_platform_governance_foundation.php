<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('preferred_locale', 5)->default('en');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestampsTz();
            $table->softDeletesTz();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('markets', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 16)->unique();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('default_locale', 5)->default('en');
            $table->string('currency_code', 3);
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();
        });

        Schema::create('legal_entities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('market_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('registration_number')->nullable();
            $table->string('tax_registration_number')->nullable();
            $table->string('currency_code', 3);
            $table->jsonb('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();
        });

        Schema::create('tax_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('legal_entity_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->decimal('rate', 8, 4)->default(0);
            $table->jsonb('configuration')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();
            $table->unique(['legal_entity_id', 'code']);
        });

        Schema::create('system_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('scope_type', 32)->default('global');
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->string('key');
            $table->jsonb('value')->nullable();
            $table->boolean('is_secret')->default(false);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampsTz();
            $table->unique(['scope_type', 'scope_id', 'key']);
        });

        Schema::create('audit_events', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 120);
            $table->string('subject_type', 160)->nullable();
            $table->string('subject_id', 80)->nullable();
            $table->uuid('correlation_id')->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->jsonb('before')->nullable();
            $table->jsonb('after')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestampTz('occurred_at')->useCurrent();
            $table->index(['subject_type', 'subject_id']);
        });

        Schema::create('content_pages', function (Blueprint $table): void {
            $table->id();
            $table->string('type', 40)->default('page');
            $table->string('slug')->unique();
            $table->string('status', 24)->default('draft');
            $table->timestampTz('published_at')->nullable();
            $table->timestampTz('scheduled_for')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampsTz();
            $table->softDeletesTz();
        });

        Schema::create('content_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('content_page_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->string('navigation_label')->nullable();
            $table->text('summary')->nullable();
            $table->longText('body')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->jsonb('structured_data')->nullable();
            $table->boolean('translation_approved')->default(false);
            $table->timestampsTz();
            $table->unique(['content_page_id', 'locale']);
        });

        Schema::create('media_assets', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('disk');
            $table->string('path');
            $table->string('visibility', 16)->default('private');
            $table->string('mime_type', 120);
            $table->unsignedBigInteger('size_bytes');
            $table->string('original_name');
            $table->string('alt_text_en')->nullable();
            $table->string('alt_text_ar')->nullable();
            $table->string('source_owner')->nullable();
            $table->string('usage_status', 24)->default('pending');
            $table->decimal('focal_x', 6, 5)->nullable();
            $table->decimal('focal_y', 6, 5)->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampsTz();
            $table->unique(['disk', 'path']);
        });

        Schema::create('proof_items', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('status', 24)->default('draft');
            $table->string('evidence_status', 24)->default('unverified');
            $table->string('title_en');
            $table->string('title_ar');
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->jsonb('evidence')->nullable();
            $table->foreignUuid('media_asset_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampTz('approved_at')->nullable();
            $table->timestampsTz();
        });

        Schema::create('form_submissions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type', 40)->default('rfq');
            $table->string('status', 24)->default('new');
            $table->string('locale', 5);
            $table->string('source_page')->nullable();
            $table->string('campaign_source')->nullable();
            $table->string('campaign_medium')->nullable();
            $table->string('campaign_name')->nullable();
            $table->string('contact_name');
            $table->string('organization_name')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('service_code')->nullable();
            $table->string('property_type')->nullable();
            $table->string('urgency')->nullable();
            $table->unsignedInteger('estimated_quantity')->nullable();
            $table->text('message')->nullable();
            $table->jsonb('payload')->nullable();
            $table->uuid('correlation_id')->index();
            $table->string('ip_hash', 64)->nullable();
            $table->timestampTz('submitted_at')->useCurrent();
            $table->timestampsTz();
        });

        Schema::create('consent_records', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('form_submission_id')->constrained()->cascadeOnDelete();
            $table->string('purpose', 80);
            $table->string('policy_version', 40);
            $table->boolean('granted');
            $table->string('locale', 5);
            $table->timestampTz('recorded_at')->useCurrent();
            $table->unique(['form_submission_id', 'purpose']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consent_records');
        Schema::dropIfExists('form_submissions');
        Schema::dropIfExists('proof_items');
        Schema::dropIfExists('media_assets');
        Schema::dropIfExists('content_translations');
        Schema::dropIfExists('content_pages');
        Schema::dropIfExists('audit_events');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('tax_profiles');
        Schema::dropIfExists('legal_entities');
        Schema::dropIfExists('markets');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
