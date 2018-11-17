<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexSingersTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('singers_tasks', function (Blueprint $table) {
			$table->unique(['singer_id','task_id'], 'ui_singers_tasks');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('singers_tasks', function (Blueprint $table) {
			$table->dropUnique('ui_singers_tasks');
		});
    }
}
