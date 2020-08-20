<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('attendances', static function (Blueprint $table) {
            $table->id();
            $table->integer('singer_id')->unsigned();
            $table->integer('event_id')->unsigned();
            $table->string('response');
            $table->timestamps();

            $table->foreign('singer_id')->references('id')->on('singers')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
}
