<?php

namespace App\Listeners;

use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        $guardType = $event->guard === 'admin' ? 'admin' : 'user';
        ActivityLogger::logAuth('logout', $event->user, $guardType);
    }
}
