<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColourToVoiceParts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('voice_parts', static function (Blueprint $table) {
            $table->string('colour');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('voice_parts', static function (Blueprint $table) {
            $table->dropColumn('colour');
        });
    }
}
