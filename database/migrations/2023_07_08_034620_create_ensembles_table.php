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
        Schema::create('ensembles', function (Blueprint $table) {
            $table->id();
			$table->string('name');
			$table->string('logo')->nullable();
			$table->string('tenant_id', 191)->nullable();
            $table->timestamps();

			$table->foreign('tenant_id')->references('id')->on('tenants');
        });

		// copy choir info from organisation/tenant
        $now = now('utc')->toDateTimeString();
        $ensembles = DB::table('tenants')
            ->select(['id', 'data'])
            ->get()
		    ->map(fn ($org) => [
                'name' => json_decode($org->data, true)['choir_name'],
                'logo' => json_decode($org->data, true)['choir_logo'] ?? null,
                'tenant_id' => $org->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

		DB::table('ensembles')->insert($ensembles->toArray());

		Schema::table('ensembles', function (Blueprint $table) {
			$table->string('tenant_id')->nullable(false)->change();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ensembles');
    }
};
