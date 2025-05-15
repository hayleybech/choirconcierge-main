<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PragmaRX\Countries\Package\Countries;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Convert AU states
        DB::table('users')
            ->whereNotNull('address_state')
            ->select(['id', 'address_state'])
            ->get()
            ->each(function ($user) {
               $user->address_state = Countries::firstWhere('cca3', 'AUS')
                   ->hydrateStates()
                   ->states
                   ->firstWhere('postal', $user->address_state)
                   ->iso_3166_2;

                  DB::table('users')
                    ->where('id', $user->id)
                    ->update(['address_state' => $user->address_state]);
            });

        // Convert states manually added by Canadians to their addresses
        DB::table('users')
            ->where('address_street_1', 'like', '%Ontario%')
            ->orWhere('address_street_2', 'like', '%Ontario%')
            ->update(['address_state' => 'CA-ON']);
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
