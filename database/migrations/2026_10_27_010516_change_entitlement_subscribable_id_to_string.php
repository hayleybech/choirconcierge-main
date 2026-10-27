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
            $table->dropIndex(['subscribable_type', 'subscribable_id']);
            $table->dropIndex(['subscribable_type', 'subscribable_id', 'status']);
            $table->string('subscribable_id', 191)->change();
            $table->index(['subscribable_type', 'subscribable_id']);
            $table->index(['subscribable_type', 'subscribable_id', 'status']);
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
