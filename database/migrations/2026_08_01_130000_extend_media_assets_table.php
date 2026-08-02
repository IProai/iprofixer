<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media_assets', function (Blueprint $table): void {
            $table->string('extension', 16)->nullable()->after('original_name');
            $table->string('checksum', 64)->nullable()->after('extension');
            $table->unsignedInteger('width')->nullable()->after('checksum');
            $table->unsignedInteger('height')->nullable()->after('width');
            $table->boolean('is_decorative')->default(false)->after('alt_text_ar');
            $table->text('caption_en')->nullable()->after('is_decorative');
            $table->text('caption_ar')->nullable()->after('caption_en');
            $table->softDeletesTz()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('media_assets', function (Blueprint $table): void {
            $table->dropSoftDeletesTz();
            $table->dropColumn([
                'extension',
                'checksum',
                'width',
                'height',
                'is_decorative',
                'caption_en',
                'caption_ar',
            ]);
        });
    }
};
