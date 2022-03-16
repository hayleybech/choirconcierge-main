<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->boolean('is_primary')->default(false);
        });

        $primaryDomains = DB::table('domains')
            ->selectRaw('max(id) as primary_domain_id')
            ->groupBy(['tenant_id'])
            ->pluck('primary_domain_id');

        DB::table('domains')
            ->whereIn('id', $primaryDomains)
            ->update(['is_primary' => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('domains', function (Blueprint $table) {
            //
        });
    }
};
