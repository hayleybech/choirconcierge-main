<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFrontRowFloorToRiserStacks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('riser_stacks', static function (Blueprint $table) {
            $table->boolean('front_row_on_floor')->default(true);
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
            $table->dropColumn('front_row_on_floor');
        });
    }
}
