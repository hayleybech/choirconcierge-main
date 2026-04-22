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
        Schema::rename('singer_categories', 'singer_statuses');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('singer_statuses', 'singer_categories');
    }
};
