<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $removeDuplicateEventMemberships = static function (string $table): void {
            $qualifiedTable = '`'.DB::getTablePrefix().$table.'`';

            DB::statement(<<<SQL
                DELETE duplicate_row
                FROM {$qualifiedTable} AS duplicate_row
                INNER JOIN {$qualifiedTable} AS kept_row
                    ON duplicate_row.event_id = kept_row.event_id
                    AND duplicate_row.membership_id = kept_row.membership_id
                    AND duplicate_row.id > kept_row.id
            SQL);
        };

        $removeDuplicateEventMemberships('rsvps');
        $removeDuplicateEventMemberships('attendances');

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
