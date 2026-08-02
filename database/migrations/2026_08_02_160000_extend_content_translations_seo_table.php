<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_translations', function (Blueprint $table): void {
            $table->string('og_title')->nullable()->after('seo_description');
            $table->text('og_description')->nullable()->after('og_title');
            $table->foreignUuid('og_image_id')->nullable()->after('og_description')->constrained('media_assets')->nullOnDelete();
            $table->boolean('is_noindex')->default(false)->after('og_image_id');
            $table->boolean('is_nofollow')->default(false)->after('is_noindex');
        });
    }

    public function down(): void
    {
        Schema::table('content_translations', function (Blueprint $table): void {
            $table->dropForeign(['og_image_id']);
            $table->dropColumn(['og_title', 'og_description', 'og_image_id', 'is_noindex', 'is_nofollow']);
        });
    }
};
