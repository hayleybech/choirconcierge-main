<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('singers', function (Blueprint $table) {
            $table->dropForeign('singers_tenant_id_foreign');
            $table->dropIndex('singers_tenant_id_foreign');
        });
        Schema::rename('singers', 'memberships');


        Schema::table('attendances', function (Blueprint $table) {
            $table->renameColumn('singer_id', 'membership_id');
        });

        Schema::table('placements', function (Blueprint $table) {
            $table->renameColumn('singer_id', 'membership_id');
        });

        Schema::rename('riser_stack_singer', 'riser_stack_membership');
        Schema::table('riser_stack_membership', function (Blueprint $table) {
            $table->renameColumn('singer_id', 'membership_id');
        });

        Schema::table('rsvps', function (Blueprint $table) {
            $table->renameColumn('singer_id', 'membership_id');
        });

        Schema::rename('singer_song', 'membership_song');
        Schema::table('membership_song', function(Blueprint $table) {
            $table->renameColumn('singer_id', 'membership_id');
        });

        Schema::rename('singers_roles', 'memberships_roles');
        Schema::table('memberships_roles', function(Blueprint $table) {
            $table->renameColumn('singer_id', 'membership_id');
        });

        Schema::table('singers_tasks', function(Blueprint $table) {
            $table->dropUnique('ui_singers_tasks');
        });
        Schema::rename('singers_tasks', 'memberships_tasks');
        Schema::table('memberships_tasks', function(Blueprint $table) {
            $table->renameColumn('singer_id', 'membership_id');
            $table->unique(['membership_id', 'task_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('memberships', 'singers');
    }
};
