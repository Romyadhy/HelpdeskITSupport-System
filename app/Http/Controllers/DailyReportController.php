<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\Task;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyReportController extends Controller
{
    public function index()
{
    $user = Auth::user();
    $today = now()->toDateString();

    // Cek laporan hari ini (hanya untuk support)
    $hasReportToday = $user->hasRole('support')
        ? DailyReport::where('user_id', $user->id)->whereDate('report_date', $today)->exists()
        : false;

    // Ambil daftar laporan (support = laporan pribadi, admin/manager = semua laporan)
    if ($user->hasRole('support')) {
        $dailyReports = DailyReport::with(['tasks', 'tickets', 'verifier'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();
    } else {
        $dailyReports = DailyReport::with(['user', 'tasks', 'tickets', 'verifier'])
            ->latest()
            ->get();
    }

    // =========================
    // BAGIAN YANG BERUBAH DI SINI
    // =========================
    if ($user->hasRole('support')) {
        // Statistik untuk user support
        $monthlyReportsCount = DailyReport::where('user_id', $user->id)
            ->whereMonth('report_date', now()->month)
            ->count();

        $tasksCompletedToday = Task::whereHas('completions', function ($q) use ($user, $today) {
            $q->where('user_id', $user->id)->whereDate('created_at', $today);
        })->get();

        $ticketsClosedToday = Ticket::where('assigned_to', $user->id)
            ->where('status', 'Closed')
            ->whereDate('solved_at', $today)
            ->get();

        $ticketsActiveToday = Ticket::where('assigned_to', $user->id)
            ->whereIn('status', ['Open', 'In Progress'])
            ->whereDate('updated_at', $today)
            ->get();
    } else {
        // Statistik untuk admin/manager (lihat semua data support)
        $monthlyReportsCount = DailyReport::whereMonth('report_date', now()->month)->count();

        $tasksCompletedToday = Task::whereHas('completions', function ($q) use ($today) {
            $q->whereDate('created_at', $today);
        })->get();

        $ticketsClosedToday = Ticket::where('status', 'Closed')
            ->whereDate('solved_at', $today)
            ->get();

        $ticketsActiveToday = Ticket::whereIn('status', ['Open', 'In Progress'])
            ->whereDate('updated_at', $today)
            ->get();
    }
    // =========================

    $completedTasksCount = $tasksCompletedToday->count();
    $handledTicketsCount = $ticketsClosedToday->count() + $ticketsActiveToday->count();

    return view('frontend.Report.daily', [
        'dailyReports' => $dailyReports,
        'tasksCompletedToday' => $tasksCompletedToday,
        'ticketsClosedToday' => $ticketsClosedToday,
        'ticketsActiveToday' => $ticketsActiveToday,
        'hasReportToday' => $hasReportToday,
        'monthlyReportsCount' => $monthlyReportsCount,
        'completedTasksCount' => $completedTasksCount,
        'handledTicketsCount' => $handledTicketsCount,
    ]);
}



    public function create()
    {
        $user = Auth::user();
        $today = now()->toDateString();

         // Ambil daftar laporan harian
        if ($user->hasRole('support')) {
            $dailyReports = DailyReport::with(['tasks', 'tickets', 'verifier'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        } else {
            $dailyReports = DailyReport::with(['user', 'tasks', 'tickets', 'verifier'])
                ->latest()
                ->get();
        }

        // Cegah user bikin 2 laporan dalam 1 hari
        $existing = DailyReport::where('user_id', $user->id)->whereDate('report_date', $today)->first();

        if ($existing) {
            return redirect()->route('reports.daily')->with('warning', 'Anda sudah membuat laporan hari ini.');
        }

        // Ambil kandidat task dan ticket seperti sebelumnya
        $tasksCompletedToday = Task::whereHas('completions', function ($q) use ($user, $today) {
            $q->where('user_id', $user->id)->whereDate('created_at', $today);
        })
            ->orderBy('title')
            ->get();

        $ticketsClosedToday = Ticket::where('assigned_to', $user->id)->where('status', 'Closed')->whereDate('solved_at', $today)->orderBy('updated_at', 'desc')->get();

        $ticketsActiveToday = Ticket::where('assigned_to', $user->id)
            ->whereIn('status', ['Open', 'In Progress'])
            ->whereDate('updated_at', $today)
            ->orderBy('updated_at', 'desc')
            ->get();
        
        // dd($dailyReports);

        return view('frontend.Report.create', [
            'dailyReports ' => $dailyReports, 
            'tasksCompletedToday' => $tasksCompletedToday,
            'ticketsClosedToday' => $ticketsClosedToday,
            'ticketsActiveToday' => $ticketsActiveToday,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'task_ids' => 'array|nullable',
            'ticket_ids' => 'array|nullable',
        ]);

        $report = DailyReport::create([
            'user_id' => Auth::id(),
            'report_date' => now(),
            'content' => $request->content,
        ]);

        // Jika user tidak memilih apapun, auto-attach kandidat (fallback)
        $taskIds = $request->input('task_ids', []);
        $ticketIds = $request->input('ticket_ids', []);

        if (empty($taskIds) || empty($ticketIds)) {
            $today = now()->toDateString();
            $user = Auth::user();

            if (empty($taskIds)) {
                $taskIds = Task::whereHas('completions', function ($q) use ($user, $today) {
                    $q->where('user_id', $user->id)->whereDate('created_at', $today);
                })
                    ->pluck('id')
                    ->all();
            }

            if (empty($ticketIds)) {
                $idsClosed = Ticket::where('assigned_to', $user->id)->where('status', 'Closed')->whereDate('solved_at', $today)->pluck('id');

                $idsActive = Ticket::where('assigned_to', $user->id)
                    ->whereIn('status', ['Open', 'On Progress'])
                    ->whereDate('updated_at', $today)
                    ->pluck('id');

                $ticketIds = $idsClosed->merge($idsActive)->unique()->values()->all();
            }
        }

        if (!empty($taskIds)) {
            $report->tasks()->attach($taskIds);
        }

        if (!empty($ticketIds)) {
            $report->tickets()->attach($ticketIds);
        }

        return redirect()->route('reports.daily')->with('success', 'Laporan harian berhasil dikirim.');
    }

    public function show($id){
        $report = DailyReport::with(['user', 'tasks', 'tickets', 'verifier'])
        ->findOrFail($id);
        
        return view('frontend.Report.show', compact('report'));
    }

    public function verify($id)
    {
        $report = DailyReport::findOrFail($id);
        $report->update([
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Laporan berhasil diverifikasi.');
    }
}
