<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixUniqueForTenancy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', static function (Blueprint $table) {
            $table->dropUnique('users_email_unique');
            $table->unique(['tenant_id', 'email']);
        });

        Schema::table('singers', static function (Blueprint $table) {
            $table->dropUnique('singers_email_unique');
            $table->unique(['tenant_id', 'email']);
        });

        Schema::table('user_groups', static function (Blueprint $table) {
            $table->dropUnique('user_groups_slug_unique');
            $table->unique(['tenant_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', static function (Blueprint $table) {
            $table->dropUnique('users_email_unique');
            $table->unique('email');
        });

        Schema::table('singers', static function (Blueprint $table) {
            $table->dropUnique('singers_email_unique');
            $table->unique('email');
        });

        Schema::table('user_groups', static function (Blueprint $table) {
            $table->dropUnique('user_groups_slug_unique');
            $table->unique('slug');
        });
    }
}
