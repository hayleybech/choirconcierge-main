<?php

use App\Enums\EmailCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('email_preferences')->nullable()->after('first_day_of_week');
        });

        DB::table('users')
            ->whereNull('email_preferences')
            ->update(['email_preferences' => json_encode(EmailCategory::defaults(), JSON_THROW_ON_ERROR)]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email_preferences');
        });
    }
};
