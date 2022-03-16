<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event): void
    {
        $event->user->timestamps = false; // No need to touch updated_at for this
        $event->user->last_login = date('Y-m-d H:i:s');
        $event->user->save();
    }
}
