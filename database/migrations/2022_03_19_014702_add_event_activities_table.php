<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('event_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('event_id');
            $table->unsignedTinyInteger('order');
            $table->foreignId('activity_type_id');
            $table->unsignedInteger('song_id')->nullable();
            $table->unsignedInteger('singer_id')->nullable();
            $table->string('notes')->nullable();
            $table->unsignedTinyInteger('duration')->nullable();
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')
                ->cascadeOnDelete();

            $table->foreign('song_id')->references('id')->on('songs')
                ->cascadeOnDelete();

            $table->foreign('singer_id')->references('id')->on('singers')
                ->cascadeOnDelete();
        });
    }
};
