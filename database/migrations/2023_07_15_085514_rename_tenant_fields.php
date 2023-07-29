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
        DB::table('tenants')
            ->select(['id', 'data'])
            ->get()
            ->map(function ($org) {
                $data = json_decode($org->data, true);

                if(isset($data['choir_name'])) {
                    $data['name'] = $data['choir_name'];
                    unset($data['choir_name']);
                }

                if(isset($data['choir_logo'])) {
                    $data['logo'] = $data['choir_logo'];
                    unset($data['choir_log']);
                }

                $org->data = json_encode($data);

                return $org;
            })
            ->each(fn ($org) => DB::table('tenants')
                ->where('id', $org->id)
                ->update(['data' => $org->data])
            );
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
