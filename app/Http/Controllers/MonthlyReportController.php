<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use Illuminate\Http\Request;
use App\Models\MonthlyReport;
use Auth;

class MonthlyReportController extends Controller
{
    public function index(){
        $monthly = MonthlyReport::with('user', 'verifier')->latest()->get();
        return view('frontend.Report.monthly', compact('monthly'));
    }

    public function create(){
        $user = Auth::user();
        $month = now()->format('F Y');

        // Ambil semua daily report bulan ini
        $dailyReports = DailyReport::whereMonth('report_date', now()->month)
            ->whereYear('report_date', now()->year)
            ->get();

        // Hitung agregat
        $totalTasks = $dailyReports->flatMap->tasks->count();
        $totalTickets = $dailyReports->flatMap->tickets->count();
        $totalDaysReported = $dailyReports->count();

        return view('frontend.Report.monthly-create', [
            'month' => $month,
            'dailyReports' => $dailyReports,
            'totalTasks' => $totalTasks,
            'totalTickets' => $totalTickets,
            'totalDaysReported' => $totalDaysReported,
        ]);
    }


}
