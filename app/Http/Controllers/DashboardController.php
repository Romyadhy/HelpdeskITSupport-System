<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ADMIN DASHBOARD
        if ($user->hasRole('admin')) {
            $tickets = Ticket::select('tickets.*', 'ticket_categories.name as category_name')
                ->leftJoin('ticket_categories', 'tickets.category_id', '=', 'ticket_categories.id')
                ->with('user')
                ->latest()
                ->take(5)
                ->get();

            // Main info
            $totalTickets   = Ticket::count();
            $closedTickets  = Ticket::where('status', 'Closed')->count();
            $pendingTickets = Ticket::where('status', 'In Progress')->count();
            $openTickets    = Ticket::where('status', 'Open')->count();
            $totalUsers     = User::count();

            // SLA per kategori
            $slaData = Ticket::selectRaw('category_id, AVG(duration) as avg_duration')
                ->groupBy('category_id')
                ->with('category')
                ->get();

            $slaCategories = $slaData->map(fn($t) => $t->category->name ?? 'Unknown');
            $slaDurations  = $slaData->map(fn($t) => round($t->avg_duration ?? 0, 2));

            // Rata-rata SLA keseluruhan
            $avgSlaMinutes   = (int) round($slaDurations->avg() ?? 0);
            $avgSlaFormatted = $this->formatDuration($avgSlaMinutes);

            // Ticket Trend 30 hari terakhir
            $trendData = Ticket::selectRaw("
                    DATE(created_at) as date,
                    COUNT(*) as total_created,
                    SUM(CASE WHEN status = 'Closed' THEN 1 ELSE 0 END) as total_closed
                ")
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $trendLabels  = $trendData->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('d M');
            });

            $trendCreated = $trendData->pluck('total_created');
            $trendClosed  = $trendData->pluck('total_closed');

            return view('frontend.Dashbord.admindashboard', compact(
                'tickets',
                'totalTickets',
                'closedTickets',
                'pendingTickets',
                'openTickets',
                'totalUsers',
                'slaCategories',
                'slaDurations',
                'avgSlaMinutes',
                'avgSlaFormatted',
                'trendLabels',
                'trendCreated',
                'trendClosed'
            ));
        }

        // MANAGER DASHBOARD
        if ($user->hasRole('manager')) {
            $tickets = Ticket::select('tickets.*', 'ticket_categories.name as category_name')
                ->leftJoin('ticket_categories', 'tickets.category_id', '=', 'ticket_categories.id')
                ->with('user')
                ->latest()
                ->take(5)
                ->get();

            $totalTickets   = Ticket::count();
            $closedTickets  = Ticket::where('status', 'Closed')->count();
            $pendingTickets = Ticket::where('status', 'In Progress')->count();
            $openTickets    = Ticket::where('status', 'Open')->count();

            // SLA Statistik
            $slaData = Ticket::selectRaw('category_id, AVG(duration) as avg_duration')
                ->groupBy('category_id')
                ->with('category')
                ->get();

            $slaCategories = $slaData->map(fn($t) => $t->category->name ?? 'Unknown');
            $slaDurations  = $slaData->map(fn($t) => round($t->avg_duration ?? 0, 2));

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
            $assignedTickets = Ticket::select('tickets.*', 'ticket_categories.name as category_name')
                ->leftJoin('ticket_categories', 'tickets.category_id', '=', 'ticket_categories.id')
                ->latest()
                ->with('user')
                ->take(5)
                ->get();

            $openTickets        = Ticket::where('status', 'Open')->count();
            $inProgressTickets  = Ticket::where('status', 'In Progress')->count();
            $closedTickets      = Ticket::where('status', 'Closed')->count();
            $todayTickets       = Ticket::where('created_at', '>=', now()->startOfDay())->count();

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

            $openTicketsCount        = $userTickets->where('status', 'Open')->count();
            $inProgressTicketsCount  = $userTickets->where('status', 'In Progress')->count();
            $closedTicketsCount      = $userTickets->where('status', 'Closed')->count();

            $recentTickets = $userTickets->take(3);

            return view('frontend.Dashbord.userdahboard', compact(
                'userTickets',
                'recentTickets',
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

    /**
     * Format durasi (menit) menjadi teks yang lebih manusiawi.
     * Contoh:
     *  - 45  -> "45 menit"
     *  - 130 -> "2 jam 10 menit"
     *  - 1600 -> "1 hari 2 jam 40 menit"
     */
    private function formatDuration($minutes): string
    {
        $minutes = (int) round($minutes);

        if ($minutes <= 0) {
            return '0 menit';
        }

        $minutesInDay = 60 * 24;

        $days  = intdiv($minutes, $minutesInDay);
        $hours = intdiv($minutes % $minutesInDay, 60);
        $mins  = $minutes % 60;

        $parts = [];

        if ($days > 0) {
            $parts[] = $days . ' hari';
        }
        if ($hours > 0) {
            $parts[] = $hours . ' jam';
        }
        if ($mins > 0 && $days === 0) {
            // kalau sudah ada hari, biasanya jam sudah cukup informatif
            $parts[] = $mins . ' menit';
        }

        return implode(' ', $parts);
    }
}
