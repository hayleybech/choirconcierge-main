<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyProfilesAllowNulls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->date('dob')->nullable(true)->change();
            $table->string('phone')->nullable(true)->change();
            $table->string('ice_name')->nullable(true)->change();
            $table->string('ice_phone')->nullable(true)->change();
            $table->string('reason_for_joining')->nullable(true)->change();
            $table->string('referrer')->nullable(true)->change();
            $table->string('profession')->nullable(true)->change();
            $table->string('skills')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->date('dob')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
            $table->string('ice_name')->nullable(false)->change();
            $table->string('ice_phone')->nullable(false)->change();
            $table->string('reason_for_joining')->nullable(false)->change();
            $table->string('referrer')->nullable(false)->change();
            $table->string('profession')->nullable(false)->change();
            $table->string('skills')->nullable(false)->change();
        });
    }
}
