<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\MonthlyReport;
use App\Models\Ticket; // <-- Tambahkan ini
use App\Models\TaskCompletion; // <-- Tambahkan ini jika pakai
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <-- Tambahkan ini untuk kalkulasi

class MonthlyReportController extends Controller
{
    public function index()
    {
        // Sebaiknya filter berdasarkan hak akses (Admin & Manager)
        // Dan gunakan paginate() untuk performa
        $user = Auth::user();
        if ($user->can('view-monthly-reports')) {
            $monthlyReports = MonthlyReport::with('user') // Ambil relasi ke user Admin
                                      ->latest()
                                      ->paginate(10);
            return view('frontend.Report.monthly', compact('monthlyReports'));
        }
        abort(403); // Jika tidak punya izin
    }

    public function create(Request $request)
    {
        // Pastikan hanya Admin yang bisa mengakses
        if (!Auth::user()->hasRole('admin')) {
             abort(403);
        }

        // Tentukan bulan dan tahun (default bulan lalu, atau dari input user)
        // $year = $request->input('year', now()->subMonth()->year);
        // $month = $request->input('month', now()->subMonth()->month);
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $monthName = now()->month($month)->year($year)->format('F Y');

        // --- PENGUMPULAN DATA STATISTIK ---

        // 1. Statistik Tiket (dari tabel tickets)
        $ticketStats = Ticket::query()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw("
                COUNT(*) as total_created,
                SUM(CASE WHEN status = 'Closed' THEN 1 ELSE 0 END) as total_closed,
                AVG(duration) as avg_duration_minutes, 
                SUM(is_escalation) as total_escalated 
            ")
            ->first() // Ambil hasilnya
            ->toArray(); // Ubah jadi array

        // 2. Statistik Laporan Harian (dari tabel daily_reports)
        $dailyReportStats = DailyReport::query()
            ->whereYear('report_date', $year)
            ->whereMonth('report_date', $month)
            ->selectRaw("
                COUNT(DISTINCT user_id) as total_staff_reported,
                COUNT(*) as total_reports_submitted,
                SUM(CASE WHEN verified_at IS NOT NULL THEN 1 ELSE 0 END) as total_reports_verified
            ")
            ->first()
            ->toArray();

        // 3. Statistik Penyelesaian Tugas (dari task_completions - Opsional)
        // ... (Logika untuk menghitung persentase penyelesaian tugas) ...

        // Kirim data ini ke view create
        return view('frontend.Report.monthly-create', [
            'monthName' => $monthName,
            'year' => $year,
            'month' => $month,
            'ticketStats' => $ticketStats,
            'dailyReportStats' => $dailyReportStats,
            // 'taskCompletionStats' => $taskCompletionStats, // Jika ada
        ]);
    }

    public function store(Request $request)
    {
        // Pastikan hanya Admin yang bisa menyimpan
         if (!Auth::user()->hasRole('admin')) {
             abort(403);
         }

        $validated = $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|between:1,12',
            'summary' => 'required|string|min:20',
            // Kita perlu mengirim statistik yang dihitung di create() kembali
            // Cara paling mudah: gunakan input hidden di form create
            'ticket_stats_json' => 'required|json', 
            // 'daily_report_stats_json' => 'required|json', // Jika perlu disimpan juga
        ]);

        MonthlyReport::create([
            'user_id' => Auth::id(), // ID Admin yang membuat
            'year' => $validated['year'],
            'month' => $validated['month'],
            'summary' => $validated['summary'],
            'ticket_stats' => json_decode($validated['ticket_stats_json'], true), // Simpan sebagai JSON
        ]);

        return redirect()->route('reports.monthly')->with('success', 'Laporan bulanan berhasil dibuat.');
    }
}