<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use App\Models\TaskCompletion;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|manager']);
    }

    public function index(Request $request)
{
    $query = Activity::query()
        ->with(['causer', 'subject'])
        ->latest();
    
    // Search global
    if ($search = $request->get('search')) {
        $query->where(function ($q) use ($search) {
            $q->where('description', 'like', "%{$search}%")
              ->orWhere('log_name', 'like', "%{$search}%")
              ->orWhere('event', 'like', "%{$search}%")
              ->orWhere('properties->attributes->title', 'like', "%{$search}%")
              ->orWhere('properties->ticket_title', 'like', "%{$search}%");
        });
    }

    // Filter user
    if ($userId = $request->get('user_id')) {
        $query->where('causer_id', $userId);
    }

    // Filter date range
    if ($from = $request->get('date_from')) {
        $query->whereDate('created_at', '>=', $from);
    }
    if ($to = $request->get('date_to')) {
        $query->whereDate('created_at', '<=', $to);
    }

    // === PAGINATION ===
    $logs = $query->paginate(10)->withQueryString();
    // dd($logs);
    foreach ($logs as $log) {

        if ($log->log_name === 'task_done') {

            $completion = TaskCompletion::with('task', 'user')
                ->find($log->subject_id);

            if ($completion) {
                $log->completion = $completion;
                $log->task = $completion->task;
            }
        }
    }
    // ==============================================


    // Dropdown user
    $users = User::orderBy('name')->get(['id', 'name']);

    return view('backend.logs.index', [
        'logs'   => $logs,
        'users'  => $users,
        'filters' => [
            'search'    => $search ?? '',
            'user_id'   => $userId ?? '',
            'date_from' => $from ?? '',
            'date_to'   => $to ?? '',
        ],
    ]);
}

}
