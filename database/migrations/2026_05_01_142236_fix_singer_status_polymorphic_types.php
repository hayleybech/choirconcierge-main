<?php

use App\Enums\SingerStatus;
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
        $tables = [
            'folder_viewers' => 'viewer',
            'folder_editors' => 'editor',
            'group_members' => 'memberable',
            'group_senders' => 'sender',
        ];

        foreach ($tables as $tableName => $morphName) {
            if (!Schema::hasTable($tableName)) continue;

            $idCol = "{$morphName}_id";
            $typeCol = "{$morphName}_type";

            // Update cases where the type was App\Models\SingerStatus (old class)
            // or just SingerStatus (morph map alias)
            DB::table($tableName)
                ->whereIn($typeCol, ['App\Models\SingerStatus', 'SingerStatus'])
                ->update([
                    $typeCol => SingerStatus::class,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'folder_viewers' => 'viewer',
            'folder_editors' => 'editor',
            'group_members' => 'memberable',
            'group_senders' => 'sender',
        ];

        foreach ($tables as $tableName => $morphName) {
            if (!Schema::hasTable($tableName)) continue;

            $typeCol = "{$morphName}_type";

            DB::table($tableName)
                ->where($typeCol, SingerStatus::class)
                ->update([
                    $typeCol => 'SingerStatus',
                ]);
        }
    }
};
