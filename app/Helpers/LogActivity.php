<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class logActivity
{
    public static function add(string $logName, string $event, $subject = null, string $desc = '', array $properties = []): void
    {
        $activity = activity($logName)->event($event); 

        if (Auth::check()) {
            $activity->causedBy(Auth::user());
        }

        if ($subject) {
            $activity->performedOn($subject);
        }

        if (!empty($properties)) {
            $activity->withProperties($properties);
        }

        $activity->log($desc);
    }
}
