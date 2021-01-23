<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Task;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(DummyUserSeeder::class);
        $this->command->info('Dummy User data seeded!');

        $this->call(DummyTaskSeeder::class);
        $this->command->info('Dummy Task data seeded!');

        $this->call(DummyNotificationTemplateSeeder::class);
        $this->command->info('Dummy NotificationTemplate data seeded!');

        $this->call(DummySongSeeder::class);
        $this->command->info('Dummy Song data seeded!');

        $this->call(DummyEventSeeder::class);
        $this->command->info('Dummy Event data seeded!');

        $this->call(DummyFolderSeeder::class);
        $this->command->info('Dummy Folder data seeded!');

        $this->call(DummyDocumentSeeder::class);
        $this->command->info('Dummy Document data seeded!');

        $this->call(DummyUserGroupSeeder::class);
        $this->command->info('Dummy MailingList (UserGroup) data seeded!');
    }
}
