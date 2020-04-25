<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoicePartsTable extends Migration
{
    private $part_ids;

    /*
     * Technically,
     * You could change the values in this array.
     * But I suggest you never do this,
     * Since we're migrating specific existing data.
     * It's really just to make the class read neater :D
     */
    private const PARTS = [
        'Tenor',
        'Lead',
        'Baritone',
        'Bass',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->insert();

        $this->insert_parts();

        $this->migrate_placements_up();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->migrate_placements_down();

        $this->drop();
    }

    private function insert()
    {
        Schema::create('voice_parts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });
    }

    private function insert_parts()
    {
        foreach(self::PARTS as $part){
            $this->part_ids[$part] = DB::table('voice_parts')->insertGetId(
                ['title' => $part]
            );
        }
    }

    /*
     * Voice parts were stored as strings in placements table.
     * Migrate part strings to ids.
     */
    private function migrate_placements_up()
    {
        // add voice_part_id column
        Schema::table('placements', function (Blueprint $table){
            $table->unsignedBigInteger('voice_part_id');
        });

        // insert a voice_part_id based on existing voice_part string column
        foreach (self::PARTS as $part){
            DB::table('placements')
                ->where('voice_part', '=', $part )
                ->update([ 'voice_part_id' => $this->part_ids[$part] ]);
        }

        // drop voice_part column
        Schema::table('placements', function (Blueprint $table){
            $table->dropColumn('voice_part');
        });
    }

    private function migrate_placements_down()
    {
        // re-add voice_part column
        Schema::table('placements', function (Blueprint $table){
            $table->string('voice_part');
        });

        // re-insert a voice_part string based on voice_part_id
        foreach (self::PARTS as $part){
            DB::table('placements')
                ->where('voice_part_id', '=', $this->part_ids[$part] )
                ->update([ 'voice_part' => $part ]);
        }

        // drop voice_part_id column
        Schema::table('placements', function (Blueprint $table){
            $table->dropColumn('voice_part_id');
        });
    }

    private function drop()
    {
        Schema::dropIfExists('voice_parts');
    }
}
