<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('customers', 'classic_customers');
        Schema::rename('subscriptions', 'classic_subscriptions');
    }

    public function down(): void
    {
        Schema::rename('classic_subscriptions', 'subscriptions');
        Schema::rename('classic_customers', 'customers');
    }
};
