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
        $prefix = Config::get('database.connections.mysql.prefix');
        // Need to replace null values before disallowing null
        DB::statement('UPDATE `'.$prefix.'placements` SET `experience` = "" WHERE `experience` IS NULL;');
        DB::statement('UPDATE `'.$prefix.'placements` SET `instruments` = "" WHERE `instruments` IS NULL;');
        DB::statement('UPDATE `'.$prefix.'placements` SET `skill_pitch` = 0 WHERE `skill_pitch` IS NULL;');
        DB::statement('UPDATE `'.$prefix.'placements` SET `skill_harmony` = 0 WHERE `skill_harmony` IS NULL;');
        DB::statement('UPDATE `'.$prefix.'placements` SET `skill_performance` = 0 WHERE `skill_performance` IS NULL;');
        DB::statement('UPDATE `'.$prefix.'placements` SET `skill_sightreading` = 0 WHERE `skill_sightreading` IS NULL;');
        DB::statement('UPDATE `'.$prefix.'placements` SET `voice_tone` = 0 WHERE `voice_tone` IS NULL;');
        DB::statement('UPDATE `'.$prefix.'placements` SET `voice_part` = "" WHERE `voice_part` IS NULL;');

        Schema::table('placements', function (Blueprint $table) {
            $table->string('experience')->default('')->nullable(false)->change();
            $table->string('instruments')->default('')->nullable(false)->change();
            $table->integer('skill_pitch')->default(0)->nullable(false)->change();
            $table->integer('skill_harmony')->default(0)->nullable(false)->change();
            $table->integer('skill_performance')->default(0)->nullable(false)->change();
            $table->integer('skill_sightreading')->default(0)->nullable(false)->change();
            $table->integer('voice_tone')->default(0)->nullable(false)->change();
            $table->string('voice_part')->default('')->nullable(false)->change();
        });
    }
}
