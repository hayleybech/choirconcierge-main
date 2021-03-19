<?php

use Carbon\CarbonTimeZone;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class ConvertTimezonesInEvents extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tenants = self::getTenants();

        // Update event times for each tenant
        foreach($tenants as $tenant) {
            // Convert tz name to offset
            $offset = CarbonTimeZone::create($tenant->data->timezone)->toOffsetName();
            $utc = '+0:00';

            DB::table('events')->where('tenant_id', $tenant->id)
                ->update([
                    'start_date'   => DB::raw("CONVERT_TZ(`start_date`, '{$offset}', '{$utc}')"),
                    'end_date'     => DB::raw("CONVERT_TZ(`end_date`, '{$offset}', '{$utc}')"),
                    'call_time'    => DB::raw("CONVERT_TZ(`call_time`, '{$offset}', '{$utc}')"),
                    'repeat_until' => DB::raw("CONVERT_TZ(`repeat_until`, '{$offset}', '{$utc}')"),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tenants = self::getTenants();

        // Update event times for each tenant
        foreach($tenants as $tenant) {
            // Convert tz name to offset
            $offset = CarbonTimeZone::create($tenant->data->timezone)->toOffsetName();
            $utc = '+0:00';

            DB::table('events')->where('tenant_id', $tenant->id)
                ->update([
                    'start_date'   => DB::raw("CONVERT_TZ(`start_date`, '{$utc}', '{$offset}')"),
                    'end_date'     => DB::raw("CONVERT_TZ(`end_date`, '{$utc}', '{$offset}')"),
                    'call_time'    => DB::raw("CONVERT_TZ(`call_time`, '{$utc}', '{$offset}')"),
                    'repeat_until' => DB::raw("CONVERT_TZ(`repeat_until`, '{$utc}', '{$offset}')"),
                ]);
        }
    }

    private static function getTenants(): Collection
    {
        return DB::table('tenants')->select(['id', 'data'])->get()
            ->each(static function($tenant){
                $tenant->data = json_decode($tenant->data);
            });
    }
}
