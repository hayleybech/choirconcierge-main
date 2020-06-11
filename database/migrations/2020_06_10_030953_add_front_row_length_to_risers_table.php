<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFrontRowLengthToRisersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('riser_stacks', static function (Blueprint $table) {
            $table->tinyInteger('front_row_length');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('riser_stacks', static function (Blueprint $table) {
            $table->dropColumn('front_row_length');
        });
    }
}
