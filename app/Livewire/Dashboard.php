<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\User;
use App\Models\DailyReport;
use Illuminate\Support\Facades\View;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();

        // 🧩 ADMIN DASHBOARD
        if ($user->hasRole('admin')) {
            $tickets = Ticket::select('tickets.*', 'ticket_categories.name as category_name')
                ->leftJoin('ticket_categories', 'tickets.category_id', '=', 'ticket_categories.id')
                ->with('user')
                ->latest()
                ->take(5)
                ->get();

            $totalTickets = Ticket::count();
            $closedTickets = Ticket::where('status', 'Closed')->count();
            $pendingTickets = Ticket::where('status', 'In Progress')->count();
            $openTickets = Ticket::where('status', 'Open')->count();
            $totalUsers = User::count();

            $slaData = Ticket::selectRaw('category_id, AVG(duration) as avg_duration')
                ->groupBy('category_id')
                ->with('category')
                ->get();

            $slaCategories = $slaData->map(fn($t) => $t->category->name ?? 'Unknown');
            $slaDurations = $slaData->map(fn($t) => round($t->avg_duration, 2));

            View::share('title', 'Admin Dashboard - IT Support');

            return view('frontend.Dashbord.admindashboard', compact(
                'tickets',
                'totalTickets',
                'closedTickets',
                'pendingTickets',
                'openTickets',
                'totalUsers',
                'slaCategories',
                'slaDurations'
            ))->layout('layouts.app');
        }

        // 👨‍💼 MANAGER DASHBOARD
        if ($user->hasRole('manager')) {
            $tickets = Ticket::select('tickets.*', 'ticket_categories.name as category_name')
                ->leftJoin('ticket_categories', 'tickets.category_id', '=', 'ticket_categories.id')
                ->with('user')
                ->latest()
                ->take(5)
                ->get();

            $totalTickets = Ticket::count();
            $closedTickets = Ticket::where('status', 'Closed')->count();
            $pendingTickets = Ticket::where('status', 'In Progress')->count();
            $openTickets = Ticket::where('status', 'Open')->count();

            $slaData = Ticket::selectRaw('category_id, AVG(duration) as avg_duration')
                ->groupBy('category_id')
                ->with('category')
                ->get();

            $slaCategories = $slaData->map(fn($t) => $t->category->name ?? 'Unknown');
            $slaDurations = $slaData->map(fn($t) => round($t->avg_duration, 2));

            View::share('title', 'Manager Dashboard - IT Support');

            return view('frontend.Dashbord.menagerdashboard', compact(
                'tickets',
                'totalTickets',
                'closedTickets',
                'pendingTickets',
                'openTickets',
                'slaCategories',
                'slaDurations'
            ))->layout('layouts.app');
        }

        // 🧰 SUPPORT DASHBOARD
        if ($user->hasRole('support')) {
            $assignedTickets = Ticket::select('tickets.*', 'ticket_categories.name as category_name')
                ->leftJoin('ticket_categories', 'tickets.category_id', '=', 'ticket_categories.id')
                ->latest()
                ->with('user')
                ->get();

            $openTickets = $assignedTickets->where('status', 'Open')->count();
            $inProgressTickets = $assignedTickets->where('status', 'In Progress')->count();
            $closedTickets = $assignedTickets->where('status', 'Closed')->count();
            $todayTickets = $assignedTickets->where('created_at', '>=', now()->startOfDay())->count();

            $myReports = DailyReport::where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->take(5)
                ->get();

            View::share('title', 'Support Dashboard - IT Support');

            return view('frontend.Dashbord.supportdashboard', compact(
                'assignedTickets',
                'openTickets',
                'inProgressTickets',
                'closedTickets',
                'todayTickets',
                'myReports'
            ))->layout('layouts.app');
        }

        // 👤 USER DASHBOARD
        if ($user->hasRole('user')) {
            $userTickets = Ticket::where('user_id', $user->id)
                ->latest()
                ->get();

            $openTicketsCount = $userTickets->where('status', 'Open')->count();
            $inProgressTicketsCount = $userTickets->where('status', 'In Progress')->count();
            $closedTicketsCount = $userTickets->where('status', 'Closed')->count();

            View::share('title', 'User Dashboard - IT Support');

            return view('frontend.Dashbord.userdahboard', compact(
                'userTickets',
                'openTicketsCount',
                'inProgressTicketsCount',
                'closedTicketsCount'
            ))->layout('layouts.app');
        }

        abort(403, 'Unauthorized');
    }
}
