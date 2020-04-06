<?php

use App\Models\Singer;
use App\Models\SingerCategory;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
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

        // Insert users missing for singers
        $singers_wo_users = Singer::doesntHave('user')->get();

        /** @var Singer $singer */
        foreach ($singers_wo_users as $singer) {
            $user = new User();
            $user->name = $singer->name;
            $user->email = $singer->email;
            $user->password = Str::random(10); // Require singers to create a password later.
            $user->save();

            $singer->user()->associate($user);
            $singer->save();
        }

        // Insert singers missing for users
        $users_wo_singers = User::doesntHave('singer')->get();
        /** @var User $user */
        foreach($users_wo_singers as $user) {
            $singer = new Singer();
            $singer->name = $user->name;
            $singer->email = $user->email;
            $singer->onboarding_enabled = false;
            $singer->user()->associate($user);

            // Attach to category
            $MEMBERS_CAT_ID = 3;
            $category_id = $MEMBERS_CAT_ID;
            $category = SingerCategory::find($category_id);
            $singer->category()->associate($category);

            $singer->save();

            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('singers', function (Blueprint $table) {
            $table->dropColumn('onboarding_enabled');
        });
    }
}
