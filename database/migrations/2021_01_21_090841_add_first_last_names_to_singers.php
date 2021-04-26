<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFirstLastNamesToSingers extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('singers', static function (Blueprint $table) {
            $table->string('first_name');
            $table->string('last_name')->nullable();
        });

        $singers = DB::table('singers')->select(['id', 'name'])->get();
        foreach($singers as $singer){
            $parts = explode(' ', $singer->name);
            DB::table('singers')->where('id', $singer->id)
                ->update([
                    'first_name' => $parts[0],
                    'last_name' => $parts[1] ?? '',
                ])
            ;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('singers', static function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
        });
    }
}
