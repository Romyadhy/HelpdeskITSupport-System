<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonthlyReport;
use App\Models\DailyReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonthlyReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-monthly-reports')->only(['index', 'show']);
        $this->middleware('permission:create-monthly-report')->only(['store']);
        $this->middleware('permission:edit-monthly-report')->only(['update']);
        $this->middleware('permission:delete-monthly-report')->only(['destroy']);
    }

    /**
     * GET: /api/monthly-reports
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole(['admin', 'manager'])) {
            $reports = MonthlyReport::with(['user', 'verifier'])->latest()->get();
        } else {
            $reports = MonthlyReport::where('user_id', $user->id)
                ->with(['user', 'verifier'])
                ->latest()
                ->get();
        }

        return response()->json([
            'success' => true,
            'data'    => $reports,
        ]);
    }

    /**
     * GET: /api/monthly-reports/{id}
     */
    public function show($id)
    {
        $report = MonthlyReport::with(['user', 'verifier'])->find($id);

        if (!$report) {
            return response()->json(['success' => false, 'message' => 'Report not found'], 404);
        }

        // Akses user
        if (!Auth::user()->hasRole(['admin', 'manager']) && $report->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $dailyReports = [];
        if ($report->daily_report_ids) {
            $dailyReports = DailyReport::whereIn('id', $report->daily_report_ids)->get();
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'report'       => $report,
                'dailyReports' => $dailyReports
            ],
        ]);
    }

    /**
     * POST: /api/monthly-reports
     */
    public function store(Request $request)
    {
        $period = $request->input('period', now()->format('Y-m'));
        [$year, $monthNum] = explode('-', $period);

        $validated = $request->validate([
            'content'           => 'required|string',
            'daily_report_ids'  => 'array|nullable',
        ]);

        $pickedIds = $validated['daily_report_ids'] ?? [];

        if (empty($pickedIds)) {
            $pickedIds = DailyReport::whereYear('report_date', $year)
                ->whereMonth('report_date', $monthNum)
                ->pluck('id')
                ->all();
        }

        $daily = DailyReport::whereIn('id', $pickedIds)->with(['tasks', 'tickets'])->get();

        $monthName = \Carbon\Carbon::createFromDate($year, $monthNum, 1)->translatedFormat('F');

        $report = MonthlyReport::create([
            'user_id'             => Auth::id(),
            'month'               => $monthName,
            'year'                => (int) $year,
            'report_date'         => now(),
            'content'             => $validated['content'],
            'total_days_reported' => $daily->count(),
            'total_tasks'         => $daily->flatMap->tasks->count(),
            'total_tickets'       => $daily->flatMap->tickets->count(),
            'daily_report_ids'    => array_values($pickedIds),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Monthly report for $monthName $year created successfully.",
            'data'    => $report,
        ], 201);
    }

    /**
     * PUT: /api/monthly-reports/{id}
     */
    public function update(Request $request, $id)
    {
        $report = MonthlyReport::find($id);

        if (!$report) {
            return response()->json(['success' => false, 'message' => 'Report not found'], 404);
        }

        if ($report->user_id !== Auth::id() && !Auth::user()->hasRole(['admin', 'manager'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'content'           => 'required|string',
            'daily_report_ids'  => 'array|nullable',
        ]);

        $ids = $validated['daily_report_ids'] ?? [];
        $daily = DailyReport::whereIn('id', $ids)->with(['tasks', 'tickets'])->get();

        $report->update([
            'content'             => $validated['content'],
            'daily_report_ids'    => $ids,
            'total_days_reported' => $daily->count(),
            'total_tasks'         => $daily->flatMap->tasks->count(),
            'total_tickets'       => $daily->flatMap->tickets->count(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Monthly report updated successfully.",
            'data'    => $report,
        ]);
    }

    /**
     * DELETE: /api/monthly-reports/{id}
     */
    public function destroy($id)
    {
        $report = MonthlyReport::find($id);

        if (!$report) {
            return response()->json(['success' => false, 'message' => 'Report not found'], 404);
        }

        if ($report->user_id !== Auth::id() && !Auth::user()->hasRole(['admin', 'manager'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $report->delete();

        return response()->json([
            'success' => true,
            'message' => 'Monthly report deleted successfully.',
        ]);
    }
}
