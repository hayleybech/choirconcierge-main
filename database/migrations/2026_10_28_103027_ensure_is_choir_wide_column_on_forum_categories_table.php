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
        if (! Schema::hasColumn('forum_categories', 'is_choir_wide')) {
            Schema::table('forum_categories', function (Blueprint $table) {
                $table->boolean('is_choir_wide')->default(true)->after('title');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The original table migration also defines this column, so keep it when rolling back this repair.
    }
};
