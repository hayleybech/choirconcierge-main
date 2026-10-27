<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableName = config('subscriptionify.tables.subscriptions', 'subscriptions');

        Schema::table($tableName, function (Blueprint $table): void {
            $table->dropIndex('plan_subscribable_index');
            $table->dropIndex('plan_subscribable_status_index');
            $table->string('subscribable_id', 191)->change();
            $table->index(['subscribable_type', 'subscribable_id'], 'plan_subscribable_index');
            $table->index(['subscribable_type', 'subscribable_id', 'status'], 'plan_subscribable_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new LogicException('Subscriptionify tenant IDs are strings and cannot be safely converted back to integers.');
    }
};
