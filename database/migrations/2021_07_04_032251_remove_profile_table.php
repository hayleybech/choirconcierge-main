<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class RemoveProfileTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('singers', function (Blueprint $table) {
            $table->string('reason_for_joining')->nullable();
            $table->string('referrer')->nullable();
            $table->string('membership_details')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->date('dob')->nullable();
            $table->string('phone')->nullable();
            $table->string('ice_name')->nullable();
            $table->string('ice_phone')->nullable();
            $table->string('profession')->nullable();
            $table->string('skills')->nullable();
            $table->string('address_street_1')->nullable();
            $table->string('address_street_2')->nullable();
            $table->string('address_suburb')->nullable();
            $table->string('address_state')->nullable();
            $table->string('address_postcode')->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->unsignedInteger('bha_id')->nullable();
        });

        DB::table('profiles')->get()->each(function ($profile) {
            $singer = DB::table('singers')->where('id', $profile->singer_id)->first();

            if (! $singer) {
                return false;
            }

            DB::table('singers')->where('id', $profile->singer_id)->update([
                'reason_for_joining' => $profile->reason_for_joining,
                'referrer' => $profile->referrer,
                'membership_details' => $profile->membership_details,
            ]);

            DB::table('users')->where('id', $singer->user_id)->update([
                'dob' => $profile->dob,
                'phone' => $profile->phone,
                'ice_name' => $profile->ice_name,
                'ice_phone' => $profile->ice_phone,
                'profession' => $profile->profession,
                'skills' => $profile->skills,
                'address_street_1' => $profile->address_street_1,
                'address_street_2' => $profile->address_street_2,
                'address_suburb' => $profile->address_suburb,
                'address_state' => $profile->address_state,
                'address_postcode' => $profile->address_postcode,
                'height' => $profile->height,
                'bha_id' => (int) (
                    Str::of($profile->membership_details)
                        ->replace('BHA', '')
                        ->replace('No', '')
                        ->trim()
                        ->explode(' ')[0]
                    ),
            ]);

            return true;
        });

        Schema::drop('profiles');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
}
