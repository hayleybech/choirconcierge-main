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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('billable_id', 191)->change();
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex('subscriptions_billable_id_billable_type_index');

            $table->string('billable_id', 191)->change();

            $table->index(['billable_id', 'billable_type']);
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->dropIndex('receipts_billable_id_billable_type_index');

            $table->string('billable_id', 191)->change();

            $table->index(['billable_id', 'billable_type']);
        });
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
