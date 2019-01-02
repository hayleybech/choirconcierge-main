<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('placements', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('singer_id');
			$table->string('experience');
			$table->string('instruments');
			$table->integer('skill_pitch');
			$table->integer('skill_harmony');
			$table->integer('skill_performance');
			$table->integer('skill_sightreading');
			$table->integer('voice_tone');
			$table->string('voice_part');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('placements');
    }
}
