<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFullNameFromSingers extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('singers', static function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('singers', static function (Blueprint $table) {
            $table->string('name');
        });

        $singers = DB::table('singers')->select(['id', 'first_name', 'last_name'])->get();
        foreach($singers as $singer){
            DB::table('singers')->where('id', $singer->id)
                ->update([
                    'name' => $singer->first_name.' '.$singer->last_name ?? '',
                ])
            ;
        }
    }
}
