<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('form_submissions', function (Blueprint $table): void {
            $table->string('reference', 32)->nullable()->unique()->after('id');
            $table->foreignId('assigned_to')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestampTz('last_contacted_at')->nullable()->after('submitted_at');
            $table->index(['type', 'status', 'submitted_at']);
        });
    }

    public function down(): void
    {
        Schema::table('form_submissions', function (Blueprint $table): void {
            $table->dropIndex(['type', 'status', 'submitted_at']);
            $table->dropConstrainedForeignId('assigned_to');
            $table->dropColumn(['reference', 'last_contacted_at']);
        });
    }
};
