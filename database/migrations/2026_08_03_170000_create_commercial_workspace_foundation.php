<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_groups', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampsTz();
        });

        Schema::create('organizations', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignId('group_id')->nullable()->constrained('organization_groups')->nullOnDelete();
            $table->foreignId('market_id')->nullable()->constrained('markets')->nullOnDelete();
            $table->string('name');
            $table->string('type', 32)->default('prospect'); // prospect, client, partner, inactive
            $table->string('website')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('email')->nullable();
            $table->jsonb('address')->nullable();
            $table->string('duplicate_status', 24)->default('none'); // none, suspected, confirmed, overridden
            $table->uuid('duplicate_of_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->index(['type', 'duplicate_status']);
            $table->index('name');
        });

        Schema::create('properties', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('market_id')->nullable()->constrained('markets')->nullOnDelete();
            $table->string('name');
            $table->string('type', 40)->default('hotel'); // hotel, restaurant, catering, events, other
            $table->jsonb('address')->nullable();
            $table->string('phone', 40)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampsTz();
            $table->softDeletesTz();
        });

        Schema::create('contacts', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignUuid('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->string('salutation', 16)->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('job_title')->nullable();
            $table->string('role_type', 32)->default('stakeholder'); // decision_maker, influencer, stakeholder, procurement, fb_manager
            $table->boolean('is_primary')->default(false);
            $table->string('locale', 5)->default('en');
            $table->text('notes')->nullable();
            $table->string('duplicate_status', 24)->default('none');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->index('email');
        });

        Schema::create('leads', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('form_submission_id')->nullable()->constrained('form_submissions')->nullOnDelete();
            $table->foreignUuid('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->foreignUuid('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->foreignUuid('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 24)->default('new'); // new, contacted, qualified, disqualified, converted
            $table->string('source', 40)->default('rfq'); // rfq, direct, referral, campaign, other
            $table->string('source_detail')->nullable();
            $table->string('service_code', 80)->nullable();
            $table->string('property_type', 40)->nullable();
            $table->string('urgency', 24)->nullable();
            $table->unsignedInteger('estimated_quantity')->nullable();
            $table->string('budget_indication')->nullable();
            $table->text('qualification_notes')->nullable();
            $table->string('disqualification_reason')->nullable();
            $table->timestampTz('qualified_at')->nullable();
            $table->timestampTz('disqualified_at')->nullable();
            $table->timestampTz('converted_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->index(['status', 'source']);
            $table->index('assigned_to');
        });

        Schema::create('opportunities', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->foreignUuid('organization_id')->constrained('organizations')->restrictOnDelete();
            $table->foreignUuid('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->foreignUuid('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('stage', 32)->default('discovery'); // discovery, assessment, proposal, negotiation, won, lost
            $table->unsignedTinyInteger('probability')->default(10); // 0-100
            $table->string('service_code', 80)->nullable();
            $table->decimal('estimated_value', 14, 2)->nullable();
            $table->string('currency_code', 3)->default('AED');
            $table->date('expected_close_date')->nullable();
            $table->string('next_action')->nullable();
            $table->timestampTz('next_action_due_at')->nullable();
            $table->string('loss_reason')->nullable();
            $table->text('loss_notes')->nullable();
            $table->text('notes')->nullable();
            $table->timestampTz('won_at')->nullable();
            $table->timestampTz('lost_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->index(['stage', 'assigned_to']);
        });

        Schema::create('crm_activities', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('subject_type', 160);
            $table->string('subject_id', 80);
            $table->string('type', 32)->default('note'); // call, meeting, email, note, site_visit, other
            $table->string('direction', 16)->nullable(); // inbound, outbound
            $table->string('title');
            $table->text('body')->nullable();
            $table->timestampTz('occurred_at')->useCurrent();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampsTz();
            $table->index(['subject_type', 'subject_id']);
        });

        Schema::create('crm_tasks', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('subject_type', 160);
            $table->string('subject_id', 80);
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestampTz('due_at');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampTz('completed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampsTz();
            $table->index(['subject_type', 'subject_id']);
            $table->index(['assigned_to', 'due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_tasks');
        Schema::dropIfExists('crm_activities');
        Schema::dropIfExists('opportunities');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('properties');
        Schema::dropIfExists('organizations');
        Schema::dropIfExists('organization_groups');
    }
};
