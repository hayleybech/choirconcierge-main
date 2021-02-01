<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressToProfiles extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('profiles', static function (Blueprint $table) {
            $table->string('address_street_1')->nullable();
            $table->string('address_suburb')->nullable();
            $table->string('address_state')->nullable();
            $table->string('address_postcode')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', static function (Blueprint $table) {
            $table->dropColumn('address_street_1');
            $table->dropColumn('address_suburb');
            $table->dropColumn('address_state');
            $table->dropColumn('address_postcode');
        });
    }
}
