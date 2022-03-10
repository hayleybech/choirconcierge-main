<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\Dummy\DummyDocumentSeeder;
use Database\Seeders\Dummy\DummyEventSeeder;
use Database\Seeders\Dummy\DummyFolderSeeder;
use Database\Seeders\Dummy\DummyNotificationTemplateSeeder;
use Database\Seeders\Dummy\DummySongSeeder;
use Database\Seeders\Dummy\DummyTaskSeeder;
use Database\Seeders\Dummy\DummyUserGroupSeeder;
use Database\Seeders\Dummy\DummyUserSeeder;
use Illuminate\Database\Seeder;

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
