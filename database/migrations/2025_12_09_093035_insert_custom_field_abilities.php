<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('roles')
            ->whereIn('name', [
                'Admin',
                'Music Team',
                'Membership Team',
                'Accounts Team',
                'Uniforms Team',
                'Events Team',
            ])
            ->select(['id', 'abilities'])
            ->chunkById(100, function (Collection $roles) {
                $roles->each(function ($role) {
                    $abilities = [
                        ...json_decode($role->abilities),
                        'custom_fields_view',
                        'custom_fields_create',
                        'custom_fields_update',
                        'custom_fields_delete',
                    ];

                    DB::table('roles')
                        ->where('id', $role->id)
                        ->update(['abilities' => json_encode($abilities)]);
                });
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
