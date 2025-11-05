<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\Task;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DailyReportController extends Controller
{
    /**
     * 🔹 List daily reports
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $today = now()->toDateString();

        // Support: hanya laporan dirinya sendiri
        if ($user->hasRole('support')) {
            $dailyReports = DailyReport::with(['tasks', 'tickets', 'verifier'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();

            $hasReportToday = DailyReport::where('user_id', $user->id)
                ->whereDate('report_date', $today)
                ->exists();
        } else {
            // Admin/Manager: semua laporan
            $dailyReports = DailyReport::with(['user', 'tasks', 'tickets', 'verifier'])
                ->latest()
                ->get();

            $hasReportToday = false;
        }

        // Statistik seperti di web controller
        if ($user->hasRole('support')) {
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

        return response()->json([
            'status' => 'success',
            'has_report_today' => $hasReportToday,
            'monthly_reports_count' => $monthlyReportsCount,
            'completed_tasks_count' => $tasksCompletedToday->count(),
            'handled_tickets_count' => $ticketsClosedToday->count() + $ticketsActiveToday->count(),
            'data' => $dailyReports,
        ]);
    }

    /**
     * 🔹 Show single report detail
     */
    public function show($id)
    {
        $report = DailyReport::with(['user', 'verifier', 'tasks', 'tickets'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $report,
        ]);
    }

    /**
     * 🔹 Create new daily report
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'task_ids' => 'array|nullable',
            'ticket_ids' => 'array|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Cegah duplikasi laporan di hari yang sama
        $existing = DailyReport::where('user_id', $user->id)
            ->whereDate('report_date', $today)
            ->first();

        if ($existing) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Anda sudah membuat laporan hari ini.',
            ], 400);
        }

        // Buat laporan
        $report = DailyReport::create([
            'user_id' => $user->id,
            'report_date' => $today,
            'content' => $request->content,
        ]);

        // Jika task/ticket tidak dikirim, isi otomatis
        $taskIds = $request->input('task_ids', []);
        $ticketIds = $request->input('ticket_ids', []);

        if (empty($taskIds)) {
            $taskIds = Task::whereHas('completions', function ($q) use ($user, $today) {
                $q->where('user_id', $user->id)->whereDate('created_at', $today);
            })->pluck('id')->all();
        }

        if (empty($ticketIds)) {
            $idsClosed = Ticket::where('assigned_to', $user->id)
                ->where('status', 'Closed')
                ->whereDate('solved_at', $today)
                ->pluck('id');

            $idsActive = Ticket::where('assigned_to', $user->id)
                ->whereIn('status', ['Open', 'In Progress', 'Closed'])
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

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan harian berhasil dibuat.',
            'data' => $report->load(['tasks', 'tickets']),
        ], 201);
    }

    /**
     * 🔹 Verify report (Admin/Manager only)
     */
    public function verify($id)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'manager'])) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Anda tidak memiliki izin untuk memverifikasi laporan ini.',
            ], 403);
        }

        $report = DailyReport::findOrFail($id);
        $report->update([
            'verified_by' => $user->id,
            'verified_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan berhasil diverifikasi.',
            'data' => $report,
        ]);
    }

    /**
     * 🔹 Delete report (support bisa hapus miliknya sendiri, admin bisa semua)
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $report = DailyReport::findOrFail($id);

        if ($report->user_id !== $user->id && !$user->hasRole(['admin', 'manager'])) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Anda tidak memiliki izin untuk menghapus laporan ini.',
            ], 403);
        }

        $report->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan berhasil dihapus.',
        ]);
    }
}
