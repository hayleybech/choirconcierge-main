<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('enrolments')->whereNotExists(function (Builder $query) {
            $query->select(DB::raw(1))
                ->from('voice_parts')
                ->whereColumn('voice_parts.id', 'enrolments.voice_part_id');
        })->delete();

        Schema::table('enrolments', function (Blueprint $table) {
            $table->foreign('voice_part_id')->references('id')->on('voice_parts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
