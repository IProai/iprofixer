<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rfq_notes', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('form_submission_id');
            $table->foreignId('author_id')->constrained('users')->restrictOnDelete();
            $table->text('body');
            $table->timestamps();

            $table->foreign('form_submission_id')
                ->references('id')
                ->on('form_submissions')
                ->cascadeOnDelete();
            $table->index(['form_submission_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfq_notes');
    }
};
