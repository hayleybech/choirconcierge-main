<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJoinedAtToSingers extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('singers', static function (Blueprint $table) {
            $table->timestamp('joined_at')->useCurrent();
        });

        DB::table('singers')->update([
            'joined_at' => DB::raw('`created_at`'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('singers', static function (Blueprint $table) {
            $table->dropColumn('joined_at');
        });
    }
}
