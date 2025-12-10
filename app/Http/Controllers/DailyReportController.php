<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\Task;
use App\Models\Ticket;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelPdf\Facades\Pdf;
use App\Helpers\logActivity;

class DailyReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        //Check has report today
        $hasReportToday = $user->hasRole('support')
            ? DailyReport::whereDate('report_date', $today)->exists()
            : false;

        //Get latest report
        $dailyReports = DailyReport::with(['user', 'tasks', 'tickets', 'verifier'])
            ->latest()
            ->paginate(5);

        // Total Laporan
        $monthlyReportsCount = DailyReport::whereMonth('report_date', now()->month)
            ->count();

        // Task done today
        $tasksCompletedToday = Task::whereHas('completions', function ($q) use ($today) {
            $q->whereDate('created_at', $today);
        })->get();

        // Tickets closeed per today
        $ticketsClosedToday = Ticket::where('status', 'Closed')
            ->whereDate('solved_at', $today)
            ->get();

        // Tickets Open today
        $ticketsActiveToday = Ticket::whereIn('status', ['Open', 'In Progress'])
            ->whereDate('updated_at', $today)
            ->get();

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

        $ticketsClosedToday = Ticket::where('status', 'Closed')
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

        // Warning if user try to create double reports
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
            $idsClosed = Ticket::where('status', 'Closed')
                ->whereDate('solved_at', $today)
                ->pluck('id');

            $idsActive = Ticket::whereIn('status', ['Open', 'In Progress'])
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

        // Log
        logActivity::add('daily_report', 'created', $report, 'Laporan harian dibuat', [
            'new' => $report->toArray(),
            'created_at_wita' => now()->setTimezone('Asia/Makassar')->toDateTimeString(),
        ]);

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
        $telegram = app(TelegramService::class);

        $text = "✅ <b>Laporan Diverifikasi</b>\n"
            . "User    : {$report->user->name}\n"
            . "Tanggal : {$report->report_date->format('d-m-Y')}\n"
            . 'Verifier: ' . Auth::user()->name;

        $telegram->sendMessage($text);

        // Log
        logActivity::add('daily_report', 'verified', $report, 'Laporan harian diverifikasi', [
            'old' => [
                'verified_by' => null,
                'verified_at' => null,
            ],
            'new' => [
                'verified_by' => Auth::id(),
                'verified_at' => now(),
            ],
            'verified_at_wita' => now()->setTimezone('Asia/Makassar')->toDateTimeString(),
        ]);

        return redirect()->back()->with('success', 'Laporan berhasil diverifikasi.');
    }

    public function exportPdf($id)
    {
        $user = Auth::user();
        $report = DailyReport::with([
            'user',
            'verifier',
            'tasks.completions',
            'tickets.solver',
        ])->findOrFail($id);

        if (!$user->hasRole(['admin', 'support', 'manager']) && $report->user_id !== $user->id) {
            abort(403, 'Unauthorized to export this report');
        }
        // dd($report);

        // Log
        logActivity::add('daily_report', 'exported', $report, 'Laporan harian diexport ke PDF', [
            'new' => [
                'exported_by' => auth()->user()->name,
                'exported_at' => now()->setTimezone('Asia/Makassar')->toDateTimeString(),
            ],
        ]);

        return Pdf::view('pdf.daily-report', [
            'report' => $report,
            'today' => now(),
        ])
            ->format('a4')
            ->margins(16, 16, 20, 16)
            ->name('DailyReport-' . $report->id . '.pdf');
        // ->download();
    }
}
