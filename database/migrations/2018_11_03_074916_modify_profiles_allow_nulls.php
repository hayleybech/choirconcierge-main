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
        $prefix = Config::get('database.connections.mysql.prefix');
        // Need to replace null values before disallowing null
        DB::statement('UPDATE `'.$prefix.'profiles` SET `dob` = CURRENT_TIMESTAMP WHERE `dob` IS NULL;');
        DB::statement('UPDATE `'.$prefix.'profiles` SET `phone` = "" WHERE `phone` IS NULL;');
        DB::statement('UPDATE `'.$prefix.'profiles` SET `ice_name` = "" WHERE `ice_name` IS NULL;');
        DB::statement('UPDATE `'.$prefix.'profiles` SET `ice_phone` = "" WHERE `ice_phone` IS NULL;');
        DB::statement('UPDATE `'.$prefix.'profiles` SET `reason_for_joining` = "" WHERE `reason_for_joining` IS NULL;');
        DB::statement('UPDATE `'.$prefix.'profiles` SET `referrer` = "" WHERE `referrer` IS NULL;');
        DB::statement('UPDATE `'.$prefix.'profiles` SET `profession` = "" WHERE `profession` IS NULL;');
        DB::statement('UPDATE `'.$prefix.'profiles` SET `skills` = "" WHERE `skills` IS NULL;');

        Schema::table('profiles', function (Blueprint $table) {
            $table->date('dob')->useCurrent()->nullable(false)->change();
            $table->string('phone')->default('')->nullable(false)->change();
            $table->string('ice_name')->default('')->nullable(false)->change();
            $table->string('ice_phone')->default('')->nullable(false)->change();
            $table->string('reason_for_joining')->default('')->nullable(false)->change();
            $table->string('referrer')->default('')->nullable(false)->change();
            $table->string('profession')->default('')->nullable(false)->change();
            $table->string('skills')->default('')->nullable(false)->change();
        });
    }
}
