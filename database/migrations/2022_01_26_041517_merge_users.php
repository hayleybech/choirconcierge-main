<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $emails = DB::table('users')
            ->select(['id', 'email'])
            ->get()
            ->groupBy('email');

        $emails->each(fn (Collection $email_users) => $email_users
            ->skip(1)
            ->each(function ($user) use ($email_users) {
                DB::table('singers')
                    ->where('user_id', $user->id)
                    ->update(['user_id' => $email_users->first()->id]);

                DB::table('users')->delete($user->id);
            })
        );

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'tenant_id')) {
                $table->dropForeign('users_tenant_id_foreign');
                $table->dropUnique('users_tenant_id_email_unique');
                $table->dropColumn('tenant_id');
            }
            $table->unique('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
