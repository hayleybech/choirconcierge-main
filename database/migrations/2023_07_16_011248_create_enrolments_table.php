<?php

use Illuminate\Database\Migrations\Migration;
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
        Schema::create('enrolments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('membership_id');
            $table->unsignedBigInteger('ensemble_id');
            $table->unsignedBigInteger('voice_part_id')->nullable();
            $table->timestamps();

            $table->foreign('membership_id')->references('id')->on('memberships');
            $table->foreign('ensemble_id')->references('id')->on('ensembles');
            $table->foreign('voice_part_id')->references('id')->on('voice_parts');
        });

        $ensembles = DB::table('ensembles')
            ->select('id', 'tenant_id')
            ->get();

        $now = now('utc')->toDateTimeString();

        DB::table('enrolments')
            ->insert(
                DB::table('memberships')
                    ->select(['id', 'voice_part_id', 'tenant_id'])
                    ->get()
                    ->map(fn ($membership) => [
                        'membership_id' => $membership->id,
                        'ensemble_id' => $ensembles->firstWhere('tenant_id', '=', $membership->tenant_id)->id,
                        'voice_part_id' => $membership->voice_part_id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])
                    ->all()
            );

        Schema::table('memberships', function (Blueprint $table) {
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
        Schema::dropIfExists('enrolments');
    }
};
