<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\DailyReport;
use App\Models\Task;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            // Data untuk admin dashboard
            $tickets = Ticket::with(['user', 'category'])->latest()->take(10)->get();
            // $tickets->category->name();
            // dd($tickets->toArray());
            // dd($tickets->pluck('category.name'));
            // dd($tickets);

            // $categoryName = TicketCategory::find($tickets->category_id)->name;
            // dd($categoryName);

            return view('frontend.Dashbord.admindashboard', compact('tickets'));
        }

        if ($user->hasRole('manager')) {
            $tickets = Ticket::with('category')->latest()->take(10)->get();

            $totalTickets = Ticket::count();
            $closedTickets = Ticket::where('status', 'closed')->count();
            $pendingTickets = Ticket::where('status', 'pending')->count();
            $openTickets = Ticket::where('status', 'open')->count();

            // Statistik SLA per kategori (rata-rata durasi)
            $slaData = Ticket::selectRaw('category_id, AVG(duration) as avg_duration')
                            ->groupBy('category_id')
                            ->with('category')
                            ->get();

            $slaCategories = $slaData->map(fn($t) => $t->category->name ?? 'Unknown');
            $slaDurations = $slaData->map(fn($t) => round($t->avg_duration, 2));

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

        if ($user->hasRole('support')) {
            // Data untuk support dashboard
            $assignedTickets = Ticket::where('assigned_to', $user->id)
                                   ->where('status', 'open')
                                   ->get();
            return view('frontend.Dashbord.supportdashboard', compact('assignedTickets'));
        }

        // Default user dashboard
        $userTickets = Ticket::where('user_id', $user->id)->latest()->get();
        return view('frontend.Dashbord.userdahboard', compact('userTickets'));
    }
}
