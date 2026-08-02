<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_page_revisions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignId('content_page_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('revision_number');
            $table->string('status', 32);
            $table->json('snapshot');
            $table->text('change_summary')->nullable();
            $table->timestamps();

            $table->unique(['content_page_id', 'revision_number']);
            $table->index(['content_page_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_page_revisions');
    }
};
