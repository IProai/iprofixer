<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redirect_rules', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('source_path', 255)->unique();
            $table->string('destination_type', 24)->default('custom_url');
            $table->string('destination_path', 500);
            $table->string('route_name')->nullable();
            $table->foreignId('content_page_id')->nullable()->constrained('content_pages')->nullOnDelete();
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->boolean('is_active')->default(true);
            $table->string('locale', 5)->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('hit_count')->default(0);
            $table->timestampTz('last_hit_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redirect_rules');
    }
};
