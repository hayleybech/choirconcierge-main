<?php

use App\Models\Tenant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTenantIdToMainTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $tenant_url = parse_url( config('app.url') )['host'];  // clientname.choirconcierge.com
        $tenant_name = explode( '.', $tenant_url )[0];     // clientname
        $tenant = $this->get_or_create_tenant($tenant_name, $tenant_url);

        // Primary Models
        //
        // Event
        // EventType
        // Folder
        // RiserStack
        // Role
        // Singer
        // SingerCategory
        // Song
        // SongAttachmentCategory
        // SongCategory
        // SongStatus
        // Task
        // User
        // UserGroup
        // VoicePart

        Schema::table('events', static function (Blueprint $table) use($tenant_name) {
            $table->string('tenant_id')->default($tenant_name);
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('event_types', static function (Blueprint $table) use($tenant_name) {
            $table->string('tenant_id')->default($tenant_name);
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('folders', static function (Blueprint $table) use($tenant_name) {
            $table->string('tenant_id')->default($tenant_name);
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('riser_stacks', static function (Blueprint $table) use($tenant_name) {
            $table->string('tenant_id')->default($tenant_name);
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('roles', static function (Blueprint $table) use($tenant_name) {
            $table->string('tenant_id')->default($tenant_name);
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('singers', static function (Blueprint $table) use($tenant_name) {
            $table->string('tenant_id')->default($tenant_name);
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('singer_categories', static function (Blueprint $table) use($tenant_name) {
            $table->string('tenant_id')->default($tenant_name);
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('songs', static function (Blueprint $table) use($tenant_name) {
            $table->string('tenant_id')->default($tenant_name);
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('song_attachment_categories', static function (Blueprint $table) use($tenant_name) {
            $table->string('tenant_id')->default($tenant_name);
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('song_categories', static function (Blueprint $table) use($tenant_name) {
            $table->string('tenant_id')->default($tenant_name);
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('song_statuses', static function (Blueprint $table) use($tenant_name) {
            $table->string('tenant_id')->default($tenant_name);
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('tasks', static function (Blueprint $table) use($tenant_name) {
            $table->string('tenant_id')->default($tenant_name);
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('users', static function (Blueprint $table) use($tenant_name) {
            $table->string('tenant_id')->default($tenant_name);
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('user_groups', static function (Blueprint $table) use($tenant_name) {
            $table->string('tenant_id')->default($tenant_name);
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('voice_parts', static function (Blueprint $table) use($tenant_name) {
            $table->string('tenant_id')->default($tenant_name);
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('voice_parts', static function (Blueprint $table) {
            $table->dropForeign('voice_parts_tenant_id_foreign');
            $table->dropColumn('tenant_id');
        });

        Schema::table('user_groups', static function (Blueprint $table) {
            $table->dropForeign('user_groups_tenant_id_foreign');
            $table->dropColumn('tenant_id');
        });

        Schema::table('users', static function (Blueprint $table) {
            $table->dropForeign('users_tenant_id_foreign');
            $table->dropColumn('tenant_id');
        });

        Schema::table('tasks', static function (Blueprint $table) {
            $table->dropForeign('tasks_tenant_id_foreign');
            $table->dropColumn('tenant_id');
        });

        Schema::table('song_statuses', static function (Blueprint $table) {
            $table->dropForeign('song_statuses_tenant_id_foreign');
            $table->dropColumn('tenant_id');
        });

        Schema::table('song_categories', static function (Blueprint $table) {
            $table->dropForeign('song_categories_tenant_id_foreign');
            $table->dropColumn('tenant_id');
        });

        Schema::table('song_attachment_categories', static function (Blueprint $table) {
            $table->dropForeign('song_attachment_categories_tenant_id_foreign');
            $table->dropColumn('tenant_id');
        });

        Schema::table('songs', static function (Blueprint $table) {
            $table->dropForeign('songs_tenant_id_foreign');
            $table->dropColumn('tenant_id');
        });

        Schema::table('singer_categories', static function (Blueprint $table) {
            $table->dropForeign('singer_categories_tenant_id_foreign');
            $table->dropColumn('tenant_id');
        });

        Schema::table('singers', static function (Blueprint $table) {
            $table->dropForeign('singers_tenant_id_foreign');
            $table->dropColumn('tenant_id');
        });

        Schema::table('roles', static function (Blueprint $table) {
            $table->dropForeign('roles_tenant_id_foreign');
            $table->dropColumn('tenant_id');
        });

        Schema::table('riser_stacks', static function (Blueprint $table) {
            $table->dropForeign('riser_stacks_tenant_id_foreign');
            $table->dropColumn('tenant_id');
        });

        Schema::table('folders', static function (Blueprint $table) {
            $table->dropForeign('folders_tenant_id_foreign');
            $table->dropColumn('tenant_id');
        });

        Schema::table('event_types', static function (Blueprint $table) {
            $table->dropForeign('event_types_tenant_id_foreign');
            $table->dropColumn('tenant_id');
        });

        Schema::table('events', static function (Blueprint $table) {
            $table->dropForeign('events_tenant_id_foreign');
            $table->dropColumn('tenant_id');
        });
    }

    public function get_or_create_tenant(string $tenant_name, string $tenant_url) {
        // First or create - but without Eloquent
        $tenant = DB::table('tenants')->where('id', '=', $tenant_name)->get()->first();
        if( ! $tenant ) {
            $tenant = DB::table('tenants')->insert(['id' => $tenant_name]);
        }

        $domain = DB::table('domains')->where('tenant_id', '=', $tenant_name)->get()->first();
        if( ! $domain ) {
            DB::table('domains')->insert(['tenant_id' => $tenant_name, 'domain' => $tenant_url]);
        }

        return $tenant;
    }
}
