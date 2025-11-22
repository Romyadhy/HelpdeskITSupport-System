<?php

namespace App\Listeners;

use App\Helpers\logActivity;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogAuthenticationActivity
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        if ($event instanceof Login) {
            logActivity::add('auth', 'login', $event->user, 'User Logged In', [
                'new' => [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'login_at_wita' => now()->setTimezone('Asia/Makassar')->toDateTimeString(),
                ],
            ]);
        }

        if ($event instanceof Logout) {
            if ($event->user) {
                logActivity::add('auth', 'logout', $event->user, 'User Logged Out', [
                    'old' => [
                        'ip' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'logout_at_wita' => now()->setTimezone('Asia/Makassar')->toDateTimeString(),
                    ],
                ]);
            }
        }
    }
}
