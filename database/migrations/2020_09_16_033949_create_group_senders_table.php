<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupSendersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('group_senders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->morphs('sender'); // Adds unsigned int sender_id and string sender_type
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('user_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_senders');
    }
}
