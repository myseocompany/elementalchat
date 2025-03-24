<?php 

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function handle(Login $event)
    {
        // Accede al usuario y establece la fecha de último inicio de sesión
        $event->user->last_login_at = new \DateTime;


        $event->user->save();
    }
}
