<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ensemble_event', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ensemble_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('event_id');
            $table->foreign('event_id')->references('id')->on('events')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ensemble_event');
    }
};
