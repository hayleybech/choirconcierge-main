<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('singer_categories') && !Schema::hasTable('singer_statuses')) {
            Schema::rename('singer_categories', 'singer_statuses');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('singer_statuses') && !Schema::hasTable('singer_categories')) {
            Schema::rename('singer_statuses', 'singer_categories');
        }
    }
};
