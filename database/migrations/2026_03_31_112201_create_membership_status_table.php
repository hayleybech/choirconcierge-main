<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('membership_status');

        Schema::create('membership_status', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->unsignedInteger('membership_id');
            $blueprint->unsignedInteger('singer_status_id');
            $blueprint->timestamps();

            $blueprint->foreign('membership_id')->references('id')->on('memberships')->onDelete('cascade');
            $blueprint->foreign('singer_status_id')->references('id')->on('singer_statuses')->onDelete('cascade');
        });

        // Migrate data
        $memberships = DB::table('memberships')->whereNotNull('singer_category_id')->get();
        foreach ($memberships as $membership) {
            DB::table('membership_status')->insert([
                'membership_id' => $membership->id,
                'singer_status_id' => $membership->singer_category_id,
                'created_at' => $membership->created_at,
                'updated_at' => $membership->updated_at,
            ]);
        }

        Schema::table('memberships', function (Blueprint $table) {
            $table->dropColumn('singer_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->unsignedInteger('singer_category_id')->nullable();
        });

        // Migrate back (lossy if many statuses exist, but take the latest)
        $statuses = DB::table('membership_status')
            ->orderBy('created_at', 'desc')
            ->get();
        
        foreach ($statuses as $status) {
            DB::table('memberships')
                ->where('id', $status->membership_id)
                ->update(['singer_category_id' => $status->singer_status_id]);
        }

        Schema::dropIfExists('membership_status');
    }
};
