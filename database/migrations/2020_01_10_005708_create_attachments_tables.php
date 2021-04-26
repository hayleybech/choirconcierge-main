<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('song_attachment_categories', function( Blueprint $table) {
            $table->increments('id');
            $table->string('title');
        }) ;
        Schema::create('song_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('filepath');
            $table->integer('song_id')->unsigned()->index();
            $table->integer('category_id')->nullable()->unsigned()->index();
            $table->timestamps();
            $table->foreign('song_id')
                ->references('id')
                ->on('songs');
            $table->foreign('category_id')
                ->references('id')
                ->on('song_attachment_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('song_attachments');
        Schema::dropIfExists('song_attachment_categories');
    }
}
