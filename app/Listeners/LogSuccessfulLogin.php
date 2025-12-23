<?php

namespace App\Listeners;

use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $guardType = $event->guard === 'admin' ? 'admin' : 'user';
        ActivityLogger::logAuth('login', $event->user, $guardType);
    }
}
