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
        Schema::table('rsvps', function (Blueprint $table): void {
            $table->unique(
                ['event_id', 'membership_id'],
                'rsvps_event_membership_unique',
            );
        });

        Schema::table('attendances', function (Blueprint $table): void {
            $table->unique(
                ['event_id', 'membership_id'],
                'attendances_event_membership_unique',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rsvps', function (Blueprint $table): void {
            $table->dropUnique('rsvps_event_membership_unique');
        });

        Schema::table('attendances', function (Blueprint $table): void {
            $table->dropUnique('attendances_event_membership_unique');
        });
    }
};
