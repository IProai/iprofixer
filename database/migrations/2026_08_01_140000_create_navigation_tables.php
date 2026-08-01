<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('navigation_menus', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('location', 32)->unique();
            $table->string('name_en');
            $table->string('name_ar');
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();
        });

        Schema::create('navigation_items', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('navigation_menu_id')->constrained('navigation_menus')->cascadeOnDelete();
            $table->foreignUuid('parent_id')->nullable()->constrained('navigation_items')->nullOnDelete();
            $table->string('label_en');
            $table->string('label_ar');
            $table->string('destination_type', 24)->default('internal_route');
            $table->string('route_name')->nullable();
            $table->foreignId('content_page_id')->nullable()->constrained('content_pages')->nullOnDelete();
            $table->string('url', 500)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('target_blank')->default(false);
            $table->string('rel')->nullable();
            $table->timestampsTz();

            $table->index(['navigation_menu_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('navigation_items');
        Schema::dropIfExists('navigation_menus');
    }
};
