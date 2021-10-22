<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;

class UpdateUserLastLoginDate
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
    public function handle(Login $event)
    {
        try {
            $user = $event->user;
            $user->anterior_login=$user->ultimo_login;
            $user->ultimo_login=now()->toDateTimeString();
            $user->save();
        } catch (\Throwable $th) {
            report($th);
        }
    }
}
