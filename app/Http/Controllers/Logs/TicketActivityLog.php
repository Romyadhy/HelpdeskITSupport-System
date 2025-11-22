<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class TicketActivityLog extends Controller
{
    public function index()
    {
        $logs = Activity::where('log_name', 'ticket')
            ->latest()
            ->with(['causer', 'subject']) // causer = user, subject = ticket
            ->paginate(20);               // pagination

        return view('backend.logs.index', compact('logs'));
    }
}
