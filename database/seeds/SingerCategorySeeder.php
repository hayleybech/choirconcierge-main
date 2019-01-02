<?php
/**
 * Created by PhpStorm.
 * User: hayde
 * Date: 11/11/2018
 * Time: 7:19 PM
 */

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SingerCategorySeeder extends Seeder
{
    public function run()
    {
        DB::table('singer_categories')->delete();

        DB::table('singer_categories')->insert([
            ['name' => 'Prospects', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Archived Prospects', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Members', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Archived Members', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        // Attach the Prospects category to all seed Singers
        $prospects = App\SingerCategory::find(1);
        $singers = App\Singer::all();
        $prospects->singers()->saveMany($singers);
    }
}