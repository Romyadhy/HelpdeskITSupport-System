<?php

namespace App\Http\Controllers;

use App\Models\MonthlyReport;
use App\Models\DailyReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelPdf\Facades\Pdf;
use App\Helpers\logActivity;
use App\Models\Task;
use App\Models\TaskCompletion;
use App\Models\Ticket;

class MonthlyReportController extends Controller
{
    public function __construct()
    {
        // Middleware Spatie untuk otomatis handle permission
        $this->middleware('permission:view-monthly-reports')->only(['index', 'show']);
        $this->middleware('permission:create-monthly-report')->only(['create', 'store']);
        $this->middleware('permission:edit-monthly-report')->only(['edit', 'update']);
        $this->middleware('permission:delete-monthly-report')->only(['destroy']);
        $this->middleware('permission:verify-daily-report')->only(['verify']);
    }

    /**
     * Tampilkan semua laporan bulanan
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil bulan & tahun sekarang
        $currentMonth = now()->month;
        $currentYear = now()->year;

        if ($user->hasRole(['admin', 'manager'])) {
            $monthlyReports = MonthlyReport::with(['user', 'verifier'])
                ->latest()
                ->get();
        } else {
            $monthlyReports = MonthlyReport::where('user_id', $user->id)
                ->with(['user', 'verifier'])
                ->latest()
                ->get();
        }

        // TOTAL LAPORAN BULAN INI
        $totalMonthlyReports = MonthlyReport::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        // TOTAL TUGAS BULAN INI
        $totalMonthlyTasks = TaskCompletion::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();


        // TOTAL TICKET BULAN INI
        $totalMonthlyTickets = Ticket::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        // $tes = $monthlyReports;
        // dd($tes);

        return view('frontend.Report.monthly', compact('monthlyReports', 'totalMonthlyReports', 'totalMonthlyTasks', 'totalMonthlyTickets'));
    }

    public function create(Request $request)
    {
        // Ambil periode dari query, default bulan berjalan
        $period = $request->query('period', now()->format('Y-m'));
        [$year, $monthNum] = explode('-', $period);

        // Ambil semua daily report di bulan tersebut
        $dailyReports = DailyReport::whereYear('report_date', $year)
            ->whereMonth('report_date', $monthNum)
            ->with(['tasks', 'tickets', 'user'])
            ->orderBy('report_date')
            ->get();

        // Agregat data
        $totalDaysReported = $dailyReports->count();
        $totalTasks = $dailyReports->flatMap->tasks->count();
        $totalTickets = $dailyReports->flatMap->tickets->unique('id')->count();

        $month = \Carbon\Carbon::createFromDate($year, $monthNum, 1)->translatedFormat('F');

        return view('frontend.Report.monthly-create', compact('dailyReports', 'totalDaysReported', 'totalTasks', 'totalTickets', 'month', 'year', 'period'));
    }

    public function store(Request $request)
    {
        // Ambil periode dari input hidden (atau query)
        $period = $request->query('period', now()->format('Y-m'));
        [$year, $monthNum] = explode('-', $period);

        // Validasi isi laporan
        $validated = $request->validate([
            'content' => ['required', 'string'],
            'daily_report_ids' => ['array', 'nullable'],
        ]);

        // Nama bulan lokal
        $monthName = \Carbon\Carbon::createFromDate($year, $monthNum, 1)->translatedFormat('F');

        // Jika user tidak memilih daily report, ambil semua laporan bulan tsb
        $pickedIds = $validated['daily_report_ids'] ?? [];
        if (empty($pickedIds)) {
            $pickedIds = DailyReport::whereYear('report_date', $year)->whereMonth('report_date', $monthNum)->pluck('id')->all();
        }

        $pickedDaily = DailyReport::whereIn('id', $pickedIds)
            ->with(['tasks', 'tickets'])
            ->get();

        // Simpan ke tabel monthly_reports
        $monthly = MonthlyReport::create([
            'user_id' => Auth::id(),
            'month' => $monthName,
            'year' => (int) $year,
            'report_date' => now(),
            'content' => $validated['content'],
            'total_days_reported' => $pickedDaily->count(),
            'total_tasks' => $pickedDaily->flatMap->tasks->count(),
            'total_tickets' => $pickedDaily->flatMap->tickets->count(),
            'daily_report_ids' => array_values($pickedIds),
            //ini kalo misal mau matikan dan aktifkan verif
            // 'status' => 'Verified',
            // 'verified_at' => now(),
        ]);

        // Log
        logActivity::add('monthly_report', 'created', $monthly, 'Laporan bulanan dibuat', [
            'new' => $monthly->toArray(),
            'created_at_wita' => now()->setTimezone('Asia/Makassar')->toDateTimeString(),
        ]);

        return redirect()
            ->route('reports.monthly.show', $monthly->id)
            ->with('success', "Laporan bulanan untuk $monthName $year berhasil dibuat.");
    }

    /**
     * Lihat detail laporan bulanan
     */
    public function show($id)
    {
        $report = MonthlyReport::with(['user', 'verifier'])->findOrFail($id);

        // Ambil laporan harian terkait
        $dailyReports = [];
        if ($report->daily_report_ids) {
            $dailyReports = DailyReport::whereIn('id', $report->daily_report_ids)->get();
        }

        return view('frontend.Report.monthly-show', compact('report', 'dailyReports'));
    }

    /**
     * Edit laporan bulanan
     */
    public function edit($id)
    {
        $report = MonthlyReport::findOrFail($id);

        $dailyReports = DailyReport::whereMonth('report_date', now()->month)
            ->whereYear('report_date', now()->year)
            ->with(['tasks', 'tickets'])
            ->get();

        return view('frontend.Report.monthly-edit', compact('report', 'dailyReports'));
    }

    /**
     * Update laporan bulanan
     */
    public function update(Request $request, $id)
    {
        $report = MonthlyReport::findOrFail($id);

        $validated = $request->validate([
            'content' => 'required|string',
            'daily_report_ids' => 'array|nullable',
        ]);

        $dailyReportIds = $validated['daily_report_ids'] ?? [];
        $dailyReports = DailyReport::whereIn('id', $dailyReportIds)
            ->with(['tasks', 'tickets'])
            ->get();

        $old = $report->toArray();

        $report->update([
            'content' => $validated['content'],
            'daily_report_ids' => $dailyReportIds,
            'total_days_reported' => $dailyReports->count(),
            'total_tasks' => $dailyReports->flatMap->tasks->count(),
            'total_tickets' => $dailyReports->flatMap->tickets->count(),
            // 'status' => 'Verified',
        ]);

        $new = $report->toArray();

        // Log
        logActivity::add('monthly_report', 'updated', $report, 'Laporan bulanan diperbarui', [
            'old' => $old,
            'new' => $new,
            'updated_at_wita' => now()->setTimezone('Asia/Makassar')->toDateTimeString(),
        ]);

        return redirect()->route('reports.monthly.show', $report->id)->with('success', 'Laporan bulanan berhasil diperbarui.');
    }

    /**
     * Verifikasi laporan bulanan (Admin Only)
     */
    public function verify($id)
    {
        $report = MonthlyReport::findOrFail($id);

        $report->update([
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            // 'status' => 'Verified',
        ]);

        return redirect()->route('reports.monthly.show', $report->id)->with('success', 'Laporan bulanan berhasil diverifikasi.');
    }

    /**
     * Hapus laporan bulanan
     */
    public function destroy($id)
    {
        $report = MonthlyReport::findOrFail($id);
        $old = $report->toArray();
        $report->delete();

        // Log
        logActivity::add('monthly_report', 'deleted', $report, 'Laporan bulanan dihapus', [
            'old' => $old,
            'deleted_at_wita' => now()->setTimezone('Asia/Makassar')->toDateTimeString(),
        ]);

        return redirect()->route('reports.monthly')->with('success', 'Laporan bulanan berhasil dihapus.');
    }

    public function exportPdf($id)
    {
        $user = Auth::user();
        $report = MonthlyReport::with(['user', 'verifier'])->findOrFail($id);

        if (!$user->hasRole(['admin', 'manager']) && $report->user_id !== $user->id) {
            abort(403, 'Unauthorized to export this report');
        }

        // Ambil daily reports yang terhubung untuk isi tabel ringkasan
        $dailyReports = collect();
        if (!empty($report->daily_report_ids)) {
            $dailyReports = DailyReport::whereIn('id', $report->daily_report_ids)
                ->with(['tasks', 'tickets'])
                ->orderBy('report_date')
                ->get();
        }

        // Log
        logActivity::add('monthly_report', 'exported', $report, 'Laporan bulanan diexport ke PDF', [
            'new' => [
                'exported_by' => auth()->user()->name,
                'exported_at' => now()->setTimezone('Asia/Makassar')->toDateTimeString(),
            ],
        ]);

        return Pdf::view('pdf.monthly-report', [
            'report' => $report,
            'dailyReports' => $dailyReports,
            'today' => now()->setTimezone('Asia/Makassar'),
        ])
            ->format('a4')
            ->margins(16, 16, 20, 16) // top, right, bottom, left (mm)
            ->name('MonthlyReport-' . $report->id . '.pdf');
        // ->download();
    }
}
