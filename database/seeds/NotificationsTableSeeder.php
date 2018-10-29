<?php

use Illuminate\Database\Seeder;

class NotificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('notifications')->delete();

        // Create some dummy templates
        DB::table('notifications')->insert([
            [
                'template_id'		=> 1, // The base notification
                'user_id'           => 1, // The recipient
                'singer_id'         => 1, // The relevant singer
            ],
            [
                'template_id'		=> 1,
                'user_id'           => 1,
                'singer_id'         => 2,
            ],
        ]);
    }
}
