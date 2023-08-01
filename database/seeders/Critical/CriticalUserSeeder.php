<?php

namespace Database\Seeders\Critical;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CriticalUserSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * STEP 1 - Insert categories
         */

        // Insert user roles
        DB::table('roles')->insert([
            [
                'tenant_id' => tenant('id'),
                'name' => 'Admin',
                'abilities' => json_encode(
                    [
                        'singers_view',
                        'singers_create',
                        'singers_update',
                        'singers_delete',
                        'singer_profiles_view',
                        'singer_profiles_create',
                        'singer_profiles_update',
                        'singer_placements_view',
                        'singer_placements_create',
                        'singer_placements_update',
                        'voice_parts_view',
                        'voice_parts_create',
                        'voice_parts_update',
                        'voice_parts_delete',
                        'roles_view',
                        'roles_create',
                        'roles_update',
                        'roles_delete',
                        'songs_view',
                        'songs_create',
                        'songs_update',
                        'songs_delete',
                        'events_view',
                        'events_create',
                        'events_update',
                        'events_delete',
                        'attendances_view',
                        'attendances_create',
                        'attendances_update',
                        'attendances_delete',
                        'rsvps_view',
                        'folders_view',
                        'folders_create',
                        'folders_update',
                        'folders_delete',
                        'documents_view',
                        'documents_create',
                        'documents_delete',
                        'riser_stacks_view',
                        'riser_stacks_create',
                        'riser_stacks_update',
                        'riser_stacks_delete',
                        'mailing_lists_view',
                        'mailing_lists_create',
                        'mailing_lists_update',
                        'mailing_lists_delete',
                        'broadcasts_create',
                        'tasks_view',
                        'tasks_create',
                        'tasks_update',
                        'tasks_delete',
                        'notifications_view',
                        'notifications_create',
                        'notifications_update',
                        'notifications_delete',
                    ],
                    JSON_THROW_ON_ERROR,
                ),
            ],
            [
                'tenant_id' => tenant('id'),
                'name' => 'Music Team',
                'abilities' => json_encode(
                    [
                        'singers_view',
						'singers_update',
                        'singer_profiles_view',
                        'singer_placements_view',
                        'singer_placements_create',
                        'singer_placements_update',
                        'voice_parts_view',
                        'roles_view',
                        'songs_view',
                        'songs_create',
                        'songs_update',
                        'songs_delete',
                        'events_view',
                        'attendances_view',
                        'rsvps_view',
                        'folders_view',
                        'folders_create',
                        'folders_update',
                        'folders_delete',
                        'documents_view',
                        'documents_create',
                        'documents_delete',
                        'riser_stacks_view',
                        'riser_stacks_create',
                        'riser_stacks_update',
                        'riser_stacks_delete',
                        'mailing_lists_view',
                        'broadcasts_create',
                        'tasks_view',
                        'notifications_view',
                    ],
                    JSON_THROW_ON_ERROR,
                ),
            ],
            [
                'tenant_id' => tenant('id'),
                'name' => 'Membership Team',
                'abilities' => json_encode(
                    [
                        'singers_view',
                        'singers_create',
                        'singers_update',
                        'singers_delete',
                        'singer_profiles_view',
                        'singer_profiles_create',
                        'singer_profiles_update',
                        'voice_parts_view',
                        'roles_view',
                        'songs_view',
                        'song_attachments_view',
                        'events_view',
                        'attendances_view',
                        'attendances_create',
                        'attendances_update',
                        'attendances_delete',
                        'rsvps_view',
                        'folders_view',
                        'folders_create',
                        'folders_update',
                        'folders_delete',
                        'documents_view',
                        'documents_create',
                        'documents_delete',
                        'mailing_lists_view',
                        'broadcasts_create',
                        'tasks_view',
                        'notifications_view',
                    ],
                    JSON_THROW_ON_ERROR,
                ),
            ],
            [
                'tenant_id' => tenant('id'),
                'name' => 'Accounts Team',
                'abilities' => json_encode(
                    [
                        'singers_view',
						'singers_update',
                        'singer_profiles_view',
                        'voice_parts_view',
                        'songs_view',
                        'song_attachments_view',
                        'events_view',
                        'rsvps_view',
                        'folders_view',
                        'folders_create',
                        'folders_update',
                        'folders_delete',
                        'documents_view',
                        'documents_create',
                        'documents_delete',
                        'mailing_lists_view',
                        'broadcasts_create',
                        'tasks_view',
                        'notifications_view',
                    ],
                    JSON_THROW_ON_ERROR,
                ),
            ],
            [
                'tenant_id' => tenant('id'),
                'name' => 'Uniforms Team',
                'abilities' => json_encode(
                    [
                        'singers_view',
                        'singer_profiles_view',
                        'voice_parts_view',
                        'songs_view',
                        'song_attachments_view',
                        'events_view',
                        'rsvps_view',
                        'folders_view',
                        'folders_create',
                        'folders_update',
                        'folders_delete',
                        'documents_view',
                        'documents_create',
                        'documents_delete',
                        'mailing_lists_view',
                        'broadcasts_create',
                        'tasks_view',
                        'notifications_view',
                    ],
                    JSON_THROW_ON_ERROR,
                ),
            ],
            [
                'tenant_id' => tenant('id'),
                'name' => 'Events Team',
                'abilities' => json_encode(
                    [
                        'singers_view',
                        'singer_profiles_view',
                        'voice_parts_view',
                        'songs_view',
                        'song_attachments_view',
                        'events_view',
                        'events_create',
                        'events_update',
                        'events_delete',
                        'attendances_view',
                        'attendances_create',
                        'attendances_update',
                        'attendances_delete',
                        'rsvps_view',
                        'folders_view',
                        'folders_create',
                        'folders_update',
                        'folders_delete',
                        'documents_view',
                        'documents_create',
                        'documents_delete',
                        'mailing_lists_view',
                        'broadcasts_create',
                        'tasks_view',
                        'notifications_view',
                    ],
                    JSON_THROW_ON_ERROR,
                    512,
                ),
            ],
            [
                'tenant_id' => tenant('id'),
                'name' => 'User',
                'abilities' => json_encode(
                    [
                        'singers_view',
                        'singer_profiles_view',
                        'voice_parts_view',
                        'songs_view',
                        'song_attachments_view',
                        'events_view',
                        'rsvps_view',
                        'folders_view',
                        'documents_view',
                        'mailing_lists_view',
                    ],
                    JSON_THROW_ON_ERROR,
                    512,
                ),
            ],
        ]);

        // Insert singer categories
        DB::table('singer_categories')->insert([
            [
                'tenant_id' => tenant('id'),
                'name' => 'Prospects',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'tenant_id' => tenant('id'),
                'name' => 'Archived Prospects',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'tenant_id' => tenant('id'),
                'name' => 'Members',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'tenant_id' => tenant('id'),
                'name' => 'Archived Members',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        /*
         * STEP 1b - Insert Voice Parts
         */
        DB::table('voice_parts')->insert([
            ['tenant_id' => tenant('id'), 'title' => 'Tenor'],
            ['tenant_id' => tenant('id'), 'title' => 'Lead'],
            ['tenant_id' => tenant('id'), 'title' => 'Baritone'],
            ['tenant_id' => tenant('id'), 'title' => 'Bass'],
        ]);

        /*
         * STEP 2 - Insert Admin
         */
        User::firstOrCreate([
            'email' => 'hayleybech@gmail.com',
        ], [
            'first_name' => 'Hayley',
            'last_name' => 'Bech',
            'password' => bcrypt(Str::random(30)),
        ]);
    }
}
