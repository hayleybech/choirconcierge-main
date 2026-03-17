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
        DB::table('roles')
            ->select(['id', 'name', 'abilities'])
            ->chunkById(100, function (Collection $roles) {
                $roles->each(function ($role) {
                    $abilities = json_decode($role->abilities, true) ?? [];

                    $newAbilities = ['polls_view'];

                    if ($role->name !== 'User') {
                        $newAbilities = array_merge($newAbilities, [
                            'polls_create',
                            'polls_update',
                            'polls_delete',
                        ]);
                    }

                    $abilities = array_unique(array_merge($abilities, $newAbilities));

                    DB::table('roles')
                        ->where('id', $role->id)
                        ->update(['abilities' => json_encode(array_values($abilities))]);
                });
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
