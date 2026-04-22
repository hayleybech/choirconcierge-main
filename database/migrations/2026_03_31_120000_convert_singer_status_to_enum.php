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
        // 1. Update membership_status
        if (!Schema::hasColumn('membership_status', 'status')) {
            Schema::table('membership_status', function (Blueprint $table) {
                $table->string('status')->nullable()->after('membership_id');
            });
        }

        $statuses = [];
        if (Schema::hasTable('singer_statuses')) {
            $statuses = DB::table('singer_statuses')->get();
            foreach ($statuses as $status) {
                $slug = match ($status->name) {
                    'Members' => 'members',
                    'Prospects' => 'prospects',
                    'Archived Prospects' => 'archived-prospects',
                    'Archived Members' => 'archived-members',
                    default => str($status->name)->slug()->toString(),
                };
                DB::table('membership_status')->where('singer_status_id', $status->id)->update(['status' => $slug]);
            }
        }

        Schema::table('membership_status', function (Blueprint $table) {
            $foreignKeys = collect(DB::select(
                "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                 WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?",
                [
                    DB::connection()->getDatabaseName(),
                    DB::getTablePrefix() . 'membership_status',
                    'membership_status_singer_status_id_foreign'
                ]
            ));

            if ($foreignKeys->isNotEmpty()) {
                $table->dropForeign(['singer_status_id']);
            }
            $table->dropColumn('singer_status_id');
        });

        // 2. Handle polymorphic tables
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

            Schema::table($tableName, function (Blueprint $table) use ($idCol) {
                $table->string($idCol)->change();
            });

            if (!empty($statuses)) {
                foreach ($statuses as $status) {
                    $slug = match ($status->name) {
                        'Members' => 'members',
                        'Prospects' => 'prospects',
                        'Archived Prospects' => 'archived-prospects',
                        'Archived Members' => 'archived-members',
                        default => str($status->name)->slug()->toString(),
                    };

                    DB::table($tableName)
                        ->where($typeCol, 'App\Models\SingerStatus')
                        ->where($idCol, (string)$status->id)
                        ->update([$idCol => $slug]);
                }
            }
        }

        // 3. Drop singer_statuses table
        Schema::dropIfExists('singer_statuses');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('singer_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // This is complex to reverse perfectly because we've lost IDs, but we can re-insert defaults
        $defaultStatuses = [
            'members' => 'Members',
            'prospects' => 'Prospects',
            'archived-prospects' => 'Archived Prospects',
            'archived-members' => 'Archived Members',
        ];

        $insertedIds = [];
        foreach ($defaultStatuses as $slug => $name) {
            $insertedIds[$slug] = DB::table('singer_statuses')->insertGetId([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('membership_status', function (Blueprint $table) {
            $table->unsignedBigInteger('singer_status_id')->nullable()->after('membership_id');
        });

        foreach ($insertedIds as $slug => $id) {
            DB::table('membership_status')->where('status', $slug)->update(['singer_status_id' => $id]);
        }

        Schema::table('membership_status', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // Handle polymorphic tables back to IDs (limited to defaults)
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

            foreach ($insertedIds as $slug => $id) {
                DB::table($tableName)
                    ->where($typeCol, 'App\Models\SingerStatus')
                    ->where($idCol, $slug)
                    ->update([$idCol => (string)$id]);
            }

            Schema::table($tableName, function (Blueprint $table) use ($idCol) {
                $table->unsignedBigInteger($idCol)->change();
            });
        }
    }
};
