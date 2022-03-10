<?php

namespace Database\Seeders\Dummy;

use App\Models\UserGroup;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DummyUserGroupSeeder extends Seeder
{
    public function run()
    {
        UserGroup::factory()
            ->count(10)
            ->create();
    }
}
