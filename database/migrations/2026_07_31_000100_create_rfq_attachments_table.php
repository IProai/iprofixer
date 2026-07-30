<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rfq_attachments', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('form_submission_id')->constrained('form_submissions')->cascadeOnDelete();
            $table->string('disk', 40)->default('local');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 120);
            $table->unsignedBigInteger('size_bytes');
            $table->string('sha256', 64);
            $table->timestampsTz();
            $table->unique(['disk', 'path']);
            $table->index(['form_submission_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfq_attachments');
    }
};
