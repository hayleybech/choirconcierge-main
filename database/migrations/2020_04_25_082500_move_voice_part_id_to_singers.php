<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MoveVoicePartIdToSingers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add the column
        Schema::table('singers', function (Blueprint $table) {
            $table->unsignedBigInteger('voice_part_id')->nullable();
        });

        // Migrate the data
        $part_ids = DB::table('placements')->pluck('voice_part_id', 'singer_id');
        foreach( $part_ids as $singer_id => $part_id ) {
            DB::table('singers')
                ->where('id', $singer_id)
                ->update(['voice_part_id' => $part_id]);
        }

        // Drop the column from placements table.
        Schema::table('placements', function ($table) {
            $table->dropColumn('voice_part_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Re-add the column from placements table.
        Schema::table('placements', function (Blueprint $table) {
            $table->unsignedBigInteger('voice_part_id')->nullable();
        });

        // De-migrate the data.
        $part_ids = DB::table('singers')->pluck('voice_part_id', 'id');
        foreach( $part_ids as $singer_id => $part_id ) {
            DB::table('placements')
                ->where('singer_id', $singer_id)
                ->update(['voice_part_id' => $part_id]);
        }

        // Drop the column from singers table.
        Schema::table('singers', function (Blueprint $table) {
            $table->dropColumn('voice_part_id');
        });
    }
}
