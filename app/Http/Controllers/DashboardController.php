<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        /**
         * ========================
         * 🧩 ADMIN DASHBOARD
         * ========================
         */
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

            // SLA per kategori (rata-rata durasi)
            $slaData = Ticket::selectRaw('category_id, AVG(duration) as avg_duration')
                ->groupBy('category_id')
                ->with('category')
                ->get();

            $slaCategories = $slaData->map(fn ($t) => $t->category->name ?? 'Unknown');
            $slaDurations = $slaData->map(fn ($t) => round($t->avg_duration, 2));

            return view('frontend.Dashbord.admindashboard', compact(
                'tickets',
                'totalTickets',
                'closedTickets',
                'pendingTickets',
                'openTickets',
                'totalUsers',
                'slaCategories',
                'slaDurations'
            ));
        }

        /**
         * ========================
         * 👨‍💼 MANAGER DASHBOARD
         * ========================
         */
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

            // SLA Statistik
            $slaData = Ticket::selectRaw('category_id, AVG(duration) as avg_duration')
                ->groupBy('category_id')
                ->with('category')
                ->get();

            $slaCategories = $slaData->map(fn ($t) => $t->category->name ?? 'Unknown');
            $slaDurations = $slaData->map(fn ($t) => round($t->avg_duration, 2));

            return view('frontend.Dashbord.menagerdashboard', compact(
                'tickets',
                'totalTickets',
                'closedTickets',
                'pendingTickets',
                'openTickets',
                'slaCategories',
                'slaDurations'
            ));
        }

        /**
         * ========================
         * 🧰 SUPPORT DASHBOARD
         * ========================
         */
        if ($user->hasRole('support')) {
            // Ambil semua tiket yang ditugaskan ke user
            $assignedTickets = Ticket::select('tickets.*', 'ticket_categories.name as category_name')
                ->leftJoin('ticket_categories', 'tickets.category_id', '=', 'ticket_categories.id')
                ->latest()
                ->with('user')
                ->get();

            // Statistik tiket
            $openTickets = $assignedTickets->where('status', 'Open')->count();
            $inProgressTickets = $assignedTickets->where('status', 'In Progress')->count();
            $closedTickets = $assignedTickets->where('status', 'Closed')->count();

            // Tiket yang dibuat hari ini
            $todayTickets = $assignedTickets->where('created_at', '>=', now()->startOfDay())->count();

            // Laporan harian user
            $myReports = DailyReport::where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->take(5)
                ->get();

            return view('frontend.Dashbord.supportdashboard', compact(
                'assignedTickets',
                'openTickets',
                'inProgressTickets',
                'closedTickets',
                'todayTickets',
                'myReports'
            ));
        }

        /**
         * ========================
         * 👤 USER DASHBOARD
         * ========================
         */
        if ($user->hasRole('user')) {
            $userTickets = Ticket::where('user_id', $user->id)
                ->latest()
                ->get();

            $openTicketsCount = $userTickets->where('status', 'Open')->count();
            $inProgressTicketsCount = $userTickets->where('status', 'In Progress')->count();
            $closedTicketsCount = $userTickets->where('status', 'Closed')->count();

            return view('frontend.Dashbord.userdahboard', compact(
                'userTickets',
                'openTicketsCount',
                'inProgressTicketsCount',
                'closedTicketsCount'
            ));
        }

        /**
         * ========================
         * 🛑 DEFAULT FALLBACK
         * ========================
         */
        abort(403, 'Unauthorized');
    }
}
