<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add singer_roles table
        Schema::create('singers_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('singer_id');
            $table->unsignedInteger('role_id');
        });

        // Insert singer role data
        $role_assignments = DB::table('users_roles')
            ->join('singers', 'users_roles.user_id', '=', 'singers.user_id')
            ->select('users_roles.role_id', 'singers.id as singer_id')
            ->get();
        DB::table('singers_roles')->insert($role_assignments
            ->map(fn ($item) => ['role_id' => $item->role_id, 'singer_id' => $item->singer_id])
            ->all());

        // Delete user roles table
        Schema::drop('users_roles');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('singers_roles', function (Blueprint $table) {
            //
        });
    }
};
