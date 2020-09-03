<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToPrimaryTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', static function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('event_types', static function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('folders', static function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('riser_stacks', static function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('roles', static function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('singers', static function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('singer_categories', static function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('songs', static function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('song_categories', static function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('song_attachment_categories', static function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('song_statuses', static function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('users', static function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('user_groups', static function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('voice_parts', static function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', static function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('event_types', static function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('folders', static function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('riser_stacks', static function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('roles', static function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('singers', static function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('singer_categories', static function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('songs', static function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('song_categories', static function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('song_attachment_categories', static function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('song_statuses', static function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('users', static function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('user_groups', static function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('voice_parts', static function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
