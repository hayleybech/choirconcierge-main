<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrated to roles:assign-poll-permissions command to prevent production performance issues
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
