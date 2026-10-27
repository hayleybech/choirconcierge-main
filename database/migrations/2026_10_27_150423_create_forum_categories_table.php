<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('forum_categories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->boolean('is_choir_wide')->default(true);
            $table->string('tenant_id', 191);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
        });

        Schema::create('ensemble_forum_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ensemble_id')->constrained()->cascadeOnDelete();
            $table->foreignId('forum_category_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['ensemble_id', 'forum_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ensemble_forum_category');
        Schema::dropIfExists('forum_categories');
    }
};
