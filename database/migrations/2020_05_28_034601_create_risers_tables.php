<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRisersTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('riser_stacks', static function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedTinyInteger('rows');
            $table->unsignedTinyInteger('columns');
            $table->timestamps();
        });

        Schema::create('riser_stack_singer', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('riser_stack_id');
            $table->unsignedBigInteger('singer_id');
            $table->tinyInteger('row');
            $table->tinyInteger('column');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riser_stack_singer');
        Schema::dropIfExists('riser_stacks');
    }
}
