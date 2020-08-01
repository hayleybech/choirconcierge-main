<?php
/**
 * Created by PhpStorm.
 * User: hayde
 * Date: 11/11/2018
 * Time: 7:19 PM
 */

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DummyUserGroupSeeder extends Seeder
{
    public function run()
    {
        factory(App\Models\UserGroup::class, 10)->create();
    }
}