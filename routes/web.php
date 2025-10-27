<?php

use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonthlyReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard Route
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->hasRole('admin')) {
            return view('frontend.Dashbord.admindashboard');
        } elseif ($user->hasRole('manager')) {
            return view('frontend.Dashbord.menagerdashboard');
        } elseif ($user->hasRole('support')) {
            return view('frontend.Dashbord.supportdashboard');
        } else {
            return view('frontend.Dashbord.userdahboard');
        }
    })->name('dashboard');

    Route::get('/dashhboard', [DashboardController::class, 'index'])->name('dashhboard');
    // Route::get('/dashboard', function () {return view('frontend.dashboard'); })->name('dashboard');

    // Tickets Route
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets/store', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('tickets.edit');
    Route::patch('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');
    Route::post('/tickets/{ticket}/start', [TicketController::class, 'start'])->name('tickets.start');
    Route::post('/tickets/{ticket}/take-over', [TicketController::class, 'takeOver'])->name('tickets.takeOver');
    Route::post('/tickets/{ticket}/close', [TicketController::class, 'close'])->name('tickets.close');
    Route::post('tickets/{ticket}/escalate', [TicketController::class, 'escalate'])->name('tickets.escalate');
    Route::post('/tickets/{ticket}/handle-escalated', [TicketController::class, 'handleEscalated'])->name('tickets.handleEscalated');

    // Tasks Route
    Route::get('tasks/daily', [TaskController::class, 'daily'])->name('tasks.daily');
    Route::get('tasks/monthly', [TaskController::class, 'monthly'])->name('tasks.monthly');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks/store', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');

    // Report Route
    // Route::get('reports/daily', [DailyReportController::class, 'index'])->name('reports.daily');
    // Route::get('reports/monthly', [MonthlyReportController::class, 'index'])->name('reports.monthly');
    // === DAILY REPORT ===
    Route::get('reports/daily', [DailyReportController::class, 'index'])->name('reports.daily');
    Route::get('reports/daily/create', [DailyReportController::class, 'create'])->name('reports.daily.create');
    Route::post('reports/daily', [DailyReportController::class, 'store'])->name('reports.daily.store');
    Route::get('reports/daily/{id}', [DailyReportController::class, 'show'])->name('reports.daily.show');
    Route::put('reports/daily/{id}/verify', [DailyReportController::class, 'verify'])->name('reports.daily.verify');

    // === MONTHLY REPORT ===
    // Route::get('reports/monthly', [MonthlyReportController::class, 'index'])->name('reports.monthly');
    // Route::get('reports/monthly/create', [MonthlyReportController::class, 'create'])->name('reports.monthly.create');
    // Route::post('reports/monthly', [MonthlyReportController::class, 'store'])->name('reports.monthly.store');
    // Route::get('reports/monthly/{id}', [MonthlyReportController::class, 'show'])->name('reports.monthly.show');
    // Route::get('reports/monthly/{id}/edit', [MonthlyReportController::class, 'edit'])->name('reports.monthly.edit');
    // Route::put('reports/monthly/{id}', [MonthlyReportController::class, 'update'])->name('reports.monthly.update');
    // Route::delete('reports/monthly/{id}', [MonthlyReportController::class, 'destroy'])->name('reports.monthly.destroy');
    // === MONTHLY REPORT ===
    Route::get('reports/monthly', [MonthlyReportController::class, 'index'])->name('reports.monthly');
    Route::get('reports/monthly/create', [MonthlyReportController::class, 'create'])->name('reports.monthly.create');
    Route::post('reports/monthly', [MonthlyReportController::class, 'store'])->name('reports.monthly.store');
    Route::get('reports/monthly/{id}', [MonthlyReportController::class, 'show'])->name('reports.monthly.show');
    Route::get('reports/monthly/{id}/edit', [MonthlyReportController::class, 'edit'])->name('reports.monthly.edit');
    Route::put('reports/monthly/{id}', [MonthlyReportController::class, 'update'])->name('reports.monthly.update');
    Route::delete('reports/monthly/{id}', [MonthlyReportController::class, 'destroy'])->name('reports.monthly.destroy');

    Route::put('reports/monthly/{id}/verify', [MonthlyReportController::class, 'verify'])->name('reports.monthly.verify');

});

require __DIR__.'/auth.php';