<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserGroup;
use Carbon\Carbon;

class DummyUserGroupSeeder extends Seeder
{
    public function run()
    {
        UserGroup::factory()->count(10)->create();
    }
}