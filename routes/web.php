<?php

use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HandbookController;
use App\Http\Controllers\MonthlyReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TicketController;
use App\Livewire\Dashboard;
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

    // Dashboard
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

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
    Route::put('/tickets/{ticket}/handle-escalated', [TicketController::class, 'handleEscalated'])->name('tickets.handleEscalated');
    Route::post('/tickets/{ticket}/cancel', [TicketController::class, 'cancel'])->name('tickets.cancel');

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

    // === DAILY REPORT ===
    Route::get('reports/daily', [DailyReportController::class, 'index'])->name('reports.daily');
    Route::get('reports/daily/create', [DailyReportController::class, 'create'])->name('reports.daily.create');
    Route::post('reports/daily', [DailyReportController::class, 'store'])->name('reports.daily.store');
    Route::get('reports/daily/{id}', [DailyReportController::class, 'show'])->name('reports.daily.show');
    Route::put('reports/daily/{id}/verify', [DailyReportController::class, 'verify'])->name('reports.daily.verify');
    Route::get('reports/daily/{id}/pdf', [DailyReportController::class, 'exportPdf'])->name('reports.daily.pdf');

    // === MONTHLY REPORT ===
    Route::get('reports/monthly', [MonthlyReportController::class, 'index'])->name('reports.monthly');
    Route::get('reports/monthly/create', [MonthlyReportController::class, 'create'])->name('reports.monthly.create');
    Route::post('reports/monthly', [MonthlyReportController::class, 'store'])->name('reports.monthly.store');
    Route::get('reports/monthly/{id}', [MonthlyReportController::class, 'show'])->name('reports.monthly.show');
    Route::get('reports/monthly/{id}/edit', [MonthlyReportController::class, 'edit'])->name('reports.monthly.edit');
    Route::put('reports/monthly/{id}', [MonthlyReportController::class, 'update'])->name('reports.monthly.update');
    Route::delete('reports/monthly/{id}', [MonthlyReportController::class, 'destroy'])->name('reports.monthly.destroy');
    Route::put('reports/monthly/{id}/verify', [MonthlyReportController::class, 'verify'])->name('reports.monthly.verify');
    Route::get('reports/monthly/{id}/pdf', [MonthlyReportController::class, 'exportPdf'])->name('reports.monthly.pdf');

    // Handbook Route
    Route::get('handbook', [HandbookController::class, 'index'])->name('handbook.index');
    Route::get('handbook/show/{id}', [HandbookController::class, 'show'])->name('handbook.show');
    Route::get('handbook/create', [HandbookController::class, 'create'])->name('handbook.create');
    Route::post('handbook/store', [HandbookController::class, 'store'])->name('handbook.store');
    Route::get('handbook/edit/{id}', [HandbookController::class, 'edit'])->name('handbook.edit');
    Route::put('handbook/update/{id}', [HandbookController::class, 'update'])->name('handbook.update');
    Route::delete('handbook/delete/{handbook}', [HandbookController::class, 'destroy'])->name('handbook.delete');
    Route::get('handbook/download/{id}', [HandbookController::class, 'downloadPdf'])->name('handbook.download');
    // Route::get('handbook/export/pdf', [HandbookController::class, 'exportPdf'])->name('handbook.export.pdf');
});

require __DIR__ . '/auth.php';
