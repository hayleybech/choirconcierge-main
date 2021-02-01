<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAbilitiesToRoles extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('roles', static function(Blueprint $table) {
            $table->json('abilities');
        });

        // ADMIN
        DB::table('roles')
            ->where('name', '=', 'Admin')
            ->update([
                'abilities' => [
                    'singers_view', 'singers_create', 'singers_update', 'singers_delete',
                    'singer_profiles_view', 'singer_profiles_create', 'singer_profiles_update',
                    'singer_placements_view', 'singer_placements_create', 'singer_placements_update',
                    'voice_parts_view', 'voice_parts_create', 'voice_parts_update', 'voice_parts_delete',
                    'roles_view', 'roles_create', 'roles_update', 'roles_delete',
                    'songs_view', 'songs_create', 'songs_update', 'songs_delete',
                    'events_view', 'events_create', 'events_update', 'events_delete',
                    'attendances_view', 'attendances_create', 'attendances_update', 'attendances_delete',
                    'rsvps_view',
                    'folders_view', 'folders_create', 'folders_update', 'folders_delete',
                    'documents_view', 'documents_create', 'documents_delete',
                    'riser_stacks_view', 'riser_stacks_create', 'riser_stacks_update', 'riser_stacks_delete',
                    'mailing_lists_view', 'mailing_lists_create', 'mailing_lists_update', 'mailing_lists_delete',
                    'tasks_view', 'tasks_create', 'tasks_update', 'tasks_delete',
                    'notifications_view', 'notifications_create', 'notifications_update', 'notifications_delete',
                ]
            ]);

        // MUSIC TEAM
        DB::table('roles')
            ->where('name', '=', 'Music Team')
            ->update([
                'abilities' => [
                    'singers_view',
                    'singer_profiles_view',
                    'singer_placements_view', 'singer_placements_create', 'singer_placements_update',
                    'voice_parts_view',
                    'roles_view',
                    'songs_view', 'songs_create', 'songs_update', 'songs_delete',
                    'events_view',
                    'attendances_view',
                    'rsvps_view',
                    'folders_view', 'folders_create', 'folders_update', 'folders_delete',
                    'documents_view', 'documents_create', 'documents_delete',
                    'riser_stacks_view', 'riser_stacks_create', 'riser_stacks_update', 'riser_stacks_delete',
                    'mailing_lists_view',
                    'tasks_view',
                    'notifications_view',
                ]
            ]);

        // MEMBERSHIP TEAM
        DB::table('roles')
            ->where('name', '=', 'Membership Team')
            ->update([
                'abilities' => [
                    'singers_view', 'singers_create', 'singers_update', 'singers_delete',
                    'singer_profiles_view', 'singer_profiles_create', 'singer_profiles_update',
                    'voice_parts_view',
                    'roles_view',
                    'songs_view',
                    'song_attachments_view',
                    'events_view',
                    'attendances_view', 'attendances_create', 'attendances_update', 'attendances_delete',
                    'rsvps_view',
                    'folders_view', 'folders_create', 'folders_update', 'folders_delete',
                    'documents_view', 'documents_create', 'documents_delete',
                    'mailing_lists_view',
                    'tasks_view',
                    'notifications_view',
                ]
            ]);

        // ACCOUNTS TEAM
        DB::table('roles')
            ->where('name', '=', 'Accounts Team')
            ->update([
                'abilities' => [
                    'singers_view',
                    'singer_profiles_view',
                    'voice_parts_view',
                    'songs_view',
                    'song_attachments_view',
                    'events_view',
                    'rsvps_view',
                    'folders_view', 'folders_create', 'folders_update', 'folders_delete',
                    'documents_view', 'documents_create', 'documents_delete',
                    'mailing_lists_view',
                    'tasks_view',
                    'notifications_view',
                ]
            ]);

        // UNIFORMS TEAM
        DB::table('roles')
            ->where('name', '=', 'Uniforms Team')
            ->update([
                'abilities' => [
                    'singers_view',
                    'singer_profiles_view',
                    'voice_parts_view',
                    'songs_view',
                    'song_attachments_view',
                    'events_view',
                    'rsvps_view',
                    'folders_view', 'folders_create', 'folders_update', 'folders_delete',
                    'documents_view', 'documents_create', 'documents_delete',
                    'mailing_lists_view',
                    'tasks_view',
                    'notifications_view',
                ]
            ]);

        // INSERT NEW DATA - FOR EACH TENANT

        // Get tenants
        $tenants = DB::table('tenants')->select('id')->get();

        foreach($tenants as $tenant) {

            // EVENTS TEAM
            DB::table('roles')
                ->insert([
                    'name' => 'Events Team',
                    'tenant_id' => $tenant->id,
                    'abilities' => json_encode([
                        'singers_view',
                        'singer_profiles_view',
                        'voice_parts_view',
                        'songs_view',
                        'song_attachments_view',
                        'events_view', 'events_create', 'events_update', 'events_delete',
                        'attendances_view', 'attendances_create', 'attendances_update', 'attendances_delete',
                        'rsvps_view',
                        'folders_view', 'folders_create', 'folders_update', 'folders_delete',
                        'documents_view', 'documents_create', 'documents_delete',
                        'mailing_lists_view',
                        'tasks_view',
                        'notifications_view',
                    ], JSON_THROW_ON_ERROR, 512)
                ]);

            // USER
            $user_role_id = DB::table('roles')
                ->insertGetId([
                    'name' => 'User',
                    'tenant_id' => $tenant->id,
                    'abilities' => json_encode([
                        'singers_view',
                        'singer_profiles_view',
                        'voice_parts_view',
                        'songs_view',
                        'song_attachments_view',
                        'events_view',
                        'rsvps_view',
                        'folders_view',
                        'documents_view',
                    ], JSON_THROW_ON_ERROR, 512)
                ]);

            // Update ALL users to have role "user"
            $users = DB::table('users')
                ->select('id')
                ->where('tenant_id', '=', $tenant->id)
                ->get();

            $users_roles = $users->map(static function($user) use ($user_role_id) {
                return [
                    'user_id' => $user->id,
                    'role_id' => $user_role_id,
                ];
            });

            DB::table('users_roles')
                ->insert($users_roles->toArray());
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', static function(Blueprint $table) {
            $table->dropColumn('abilities');
        });

        DB::table('roles')
            ->whereIn('name', ['Events Team', 'User'])
            ->delete();
    }
}
