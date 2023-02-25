<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('song_attachments', function (Blueprint $table) {
            $table->string('type')->nullable();
        });

        DB::table('song_attachments')
            ->join('song_attachment_categories', 'song_attachments.category_id', '=', 'song_attachment_categories.id')
            ->select('song_attachments.id', 'song_attachment_categories.title')
            ->get()
            ->each(function ($attachment) {
                DB::table('song_attachments')
                    ->where('id', '=', $attachment->id)
                    ->update(['type' => Str::of($attachment->title)->slug()]);
            });

        Schema::table('song_attachments', function (Blueprint $table) {
            $table->string('type')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('song_attachments', function (Blueprint $table) {
            //
        });
    }
};
