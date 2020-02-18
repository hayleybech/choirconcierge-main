<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSongsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('song_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('song_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('songs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('pitch_blown');
            $table->integer('status_id');
            $table->timestamps();
        });

        Schema::create('songs_song_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('song_id');
            $table->integer('category_id');
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
        Schema::dropIfExists('songs_song_categories');
        Schema::dropIfExists('songs');
        Schema::dropIfExists('song_categories');
        Schema::dropIfExists('song_statuses');
    }
}
