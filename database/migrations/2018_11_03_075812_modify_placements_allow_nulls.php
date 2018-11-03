<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyPlacementsAllowNulls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('placements', function (Blueprint $table) {
            $table->string('experience')->nullable(true)->change();
            $table->string('instruments')->nullable(true)->change();
            $table->integer('skill_pitch')->nullable(true)->change();
            $table->integer('skill_harmony')->nullable(true)->change();
            $table->integer('skill_performance')->nullable(true)->change();
            $table->integer('skill_sightreading')->nullable(true)->change();
            $table->integer('voice_tone')->nullable(true)->change();
            $table->string('voice_part')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('placements', function (Blueprint $table) {
            $table->string('experience')->nullable(false)->change();
            $table->string('instruments')->nullable(false)->change();
            $table->integer('skill_pitch')->nullable(false)->change();
            $table->integer('skill_harmony')->nullable(false)->change();
            $table->integer('skill_performance')->nullable(false)->change();
            $table->integer('skill_sightreading')->nullable(false)->change();
            $table->integer('voice_tone')->nullable(false)->change();
            $table->string('voice_part')->nullable(false)->change();
        });
    }
}
