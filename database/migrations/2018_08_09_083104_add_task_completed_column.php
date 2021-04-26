<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTaskCompletedColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('singers_tasks', function (Blueprint $table) {
			$table->boolean('completed')->default(false);
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		if (Schema::hasColumn('singers_tasks', 'completed')) {
			Schema::table('singers_tasks', function (Blueprint $table) {
				$table->dropColumn('completed');
			});
		}
    }
}
