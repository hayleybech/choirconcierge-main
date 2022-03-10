<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->dropColumn('name');
        });

        DB::table('singers')->get()->each(function ($singer) {
            DB::table('users')->where('id', $singer->user_id)->update([
                'first_name' => $singer->first_name,
                'last_name' => $singer->last_name,
            ]);
        });

        Schema::table('singers', function (Blueprint $table) {
            $table->dropForeign('singers_tenant_id_foreign');
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete()->cascadeOnUpdate();
            $table->dropColumn([
                'first_name',
                'last_name',
                'email',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('singers', function (Blueprint $table) {
            //
        });
    }
};
