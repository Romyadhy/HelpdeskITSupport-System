<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\Task;
use App\Models\Ticket;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelPdf\Facades\Pdf;

class DailyReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        // =====================================================
        // PERBAIKAN: Support bisa melihat apakah sudah membuat laporan hari ini
        // namun pengecekan tetap berdasarkan user sendiri
        // =====================================================
        // $hasReportToday = DailyReport::where('user_id', $user->id)
        //     ->whereDate('report_date', $today)
        //     ->exists();
        $hasReportToday = $user->hasRole('support')
            ? DailyReport::whereDate('report_date', $today)->exists()
            : false;
        

        // =====================================================
        // PERBAIKAN: Untuk support, sebelumnya hanya melihat laporan miliknya sendiri.
        // Sekarang support melihat SEMUA laporan support lain.
        // =====================================================
        $dailyReports = DailyReport::with(['user', 'tasks', 'tickets', 'verifier'])
            ->latest()
            ->get(); // PERBAIKAN: hilangkan filter user_id


        // =====================================================
        // PERBAIKAN: Statistik HARUS global (semua support)
        // =====================================================

        // Total laporan bulan ini (global)
        $monthlyReportsCount = DailyReport::whereMonth('report_date', now()->month)
            ->count(); // PERBAIKAN: hapus filter user_id

        // Tasks selesai hari ini (global)
        $tasksCompletedToday = Task::whereHas('completions', function ($q) use ($today) {
            $q->whereDate('created_at', $today);
        })->get(); // PERBAIKAN: hapus filter user_id

        // Tickets closed hari ini (global)
        $ticketsClosedToday = Ticket::where('status', 'Closed')
            ->whereDate('solved_at', $today)
            ->get(); // PERBAIKAN

        // Tickets aktif hari ini (global)
        $ticketsActiveToday = Ticket::whereIn('status', ['Open', 'In Progress'])
            ->whereDate('updated_at', $today)
            ->get(); // PERBAIKAN

        // =====================================================

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

        $dailyReports = DailyReport::with(['user', 'tasks', 'tickets', 'verifier'])
            ->latest()
            ->get(); 

        $existing = DailyReport::whereDate('report_date', $today)->first();

        if ($existing) {
            return redirect()->route('reports.daily')->with('warning', 'Anda sudah membuat laporan hari ini.');
        }

        $tasksCompletedToday = Task::whereHas('completions', function ($q) use ($user, $today) {
            $q->whereDate('created_at', $today);
        })
            ->orderBy('title')
            ->get();

        $ticketsClosedToday = Ticket::where('assigned_to', $user->id)
            ->where('status', 'Closed')
            ->whereDate('solved_at', $today)
            ->orderBy('updated_at', 'desc')
            ->get();

        $ticketsActiveToday = Ticket::whereIn('status', ['In Progress', 'Open'])
            ->orderBy('updated_at', 'desc')
            ->get(); 

        return view('frontend.Report.create', [
            'dailyReports ' => $dailyReports,
            'tasksCompletedToday' => $tasksCompletedToday,
            'ticketsClosedToday' => $ticketsClosedToday,
            'ticketsActiveToday' => $ticketsActiveToday,
        ]);
    }

    public function store(Request $request)
    {
        $today = now()->toDateString();

        // PERBAIKAN: validasi global agar tidak bisa submit 2x lewat POST
        if (DailyReport::whereDate('report_date', $today)->exists()) {
            return redirect()->route('reports.daily')->with('warning', 'Daily report hari ini sudah dibuat.');
        }

        $request->validate([
            'content' => 'required|string',
            'task_ids' => 'array|nullable',
            'ticket_ids' => 'array|nullable',
        ]);

        $report = DailyReport::create([
            'user_id' => Auth::id(),
            'report_date' => now(),
            'content' => $request->input('content'),
        ]);

        // Auto sync jika kosong
        $taskIds = $request->input('task_ids', []);
        $ticketIds = $request->input('ticket_ids', []);

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
            $idsClosed = Ticket::where('assigned_to', $user->id)
                ->where('status', 'Closed')
                ->whereDate('solved_at', $today)
                ->pluck('id');

            // =====================================================
            // PERBAIKAN: active ticket diambil global
            // =====================================================
            $idsActive = Ticket::whereIn('status', ['Open', 'In Progress', 'Closed'])
                ->whereDate('updated_at', $today)
                ->pluck('id');

            $ticketIds = $idsClosed->merge($idsActive)->unique()->values()->all();
        }

        if (!empty($taskIds)) {
            $report->tasks()->attach($taskIds);
        }

        if (!empty($ticketIds)) {
            $report->tickets()->attach($ticketIds);
        }

        // Telegram notif
        $telegram = app(TelegramService::class);

        $text = "📝 <b>Laporan Harian </b>\n"
            . 'Oleh : ' . Auth::user()->name . "\n"
            . 'Tanggal: ' . now()->format('d-m-Y') . "\n"
            . "Ringkasan:\n"
            . substr($report->content, 0, 200) . ' ...';

        $telegram->sendMessage($text);

        return redirect()->route('reports.daily')->with('success', 'Laporan harian berhasil dikirim.');
    }

    public function show($id)
    {
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

        // Telegram notif
        $telegram = app(\App\Services\TelegramService::class);

        $text = "✅ <b>Laporan Diverifikasi</b>\n"
            . "User    : {$report->user->name}\n"
            . "Tanggal : {$report->report_date->format('d-m-Y')}\n"
            . 'Verifier: ' . Auth::user()->name;

        $telegram->sendMessage($text);

        return redirect()->back()->with('success', 'Laporan berhasil diverifikasi.');
    }

    public function exportPdf($id)
    {
        $user = Auth::user();
        $report = DailyReport::with([
            'user',
            'verifier',
            'tasks',
            'tickets',
        ])->findOrFail($id);

        if (!$user->hasRole(['admin', 'manager']) && $report->user_id !== $user->id) {
            abort(403, 'Unauthorized to export this report');
        }

        return Pdf::view('pdf.daily-report', [
            'report' => $report,
            'today' => now(),
        ])
            ->format('a4')
            ->margins(16, 16, 20, 16)
            ->name('DailyReport-' . $report->id . '.pdf');
    }
}

