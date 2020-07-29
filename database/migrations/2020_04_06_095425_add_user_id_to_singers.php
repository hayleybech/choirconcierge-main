<?php

use App\Models\Singer;
use App\Models\SingerCategory;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddUserIdToSingers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('singers', function (Blueprint $table) {
            // Add the user_id FK
            $table->unsignedInteger('user_id')->nullable();
            //$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        $this->sync_existing_users_and_singers();

        $this->insert_users_missing_for_singers();

        $this->insert_singers_missing_for_users();
    }

    /**
     * Reverse the migrations..
     *
     * @return void
     */
    public function down()
    {
        Schema::table('singers', function (Blueprint $table) {
            $table->dropColumn('onboarding_enabled');
        });
    }

    /**
     * Finds people who were added as a singer AND a user
     * (when those were completely separate features)
     * then connects the two records together.
     */
    public function sync_existing_users_and_singers(): void {
        $people_to_sync = DB::table('singers')
            ->join('users', 'users.email', '=', 'singers.email')
            ->select('users.id as user_id', 'singers.id as singer_id')
            ->get();

        foreach($people_to_sync as $person)
        {
            DB::table('singers')->where('id', $person->singer_id)
                ->update(['user_id' => $person->user_id]);
        }
    }

    public function insert_users_missing_for_singers(): void {
        $singers_wo_users = DB::table('singers')
            ->where('user_id', '=', null)
            ->get();

        /** @var Object $singer */
        foreach ($singers_wo_users as $singer) {

            $user_id = DB::table('users')->insertGetId(
                $user = [
                    'name'      => $singer->name,
                    'email'     => $singer->email,
                    'password'  => Str::random(10), // Require singers to create a password later.
                    'created_at'=> now(),
                    'updated_at'=> now(),
                ]
            );

            DB::table('singers')->where('id', $singer->id)
                ->update(['user_id' => $user_id]);
        }
    }

    public function insert_singers_missing_for_users(): void {
        $users_wo_singers = DB::table('users')
            ->whereNotExists(static function(Builder $query){
                $prefix = Config::get('database.connections.mysql.prefix');

                $query->select(DB::raw(1))
                    ->from('singers')
                    ->whereRaw($prefix.'singers.user_id = '.$prefix.'users.id');
            })
        ->get();

        $MEMBERS_CAT_ID = 3;

        $singers_to_insert = [];
        /** @var User $user */
        foreach($users_wo_singers as $user) {

            $singers_to_insert[] = [
                'name'                  => $user->name,
                'email'                 => $user->email,
                'onboarding_enabled'    => false,
                'user_id'               => $user->id,
                'singer_category_id'    => $MEMBERS_CAT_ID,
                'created_at'            => now(),
                'updated_at'            => now(),
            ];
        }
        DB::table('singers')
            ->insert($singers_to_insert);
    }

}
