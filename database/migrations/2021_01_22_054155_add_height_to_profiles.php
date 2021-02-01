<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHeightToProfiles extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('profiles', static function (Blueprint $table) {
            $table->unsignedDecimal('height', 5, 2)->nullable(); // e.g. 167.64 (cm)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', static function (Blueprint $table) {
            $table->dropColumn('height');
        });
    }
}
