<?php

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
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks/store', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{ticket}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::patch('/tasks/{ticket}', [TaskController::class, 'update'])->name('tasks.update');
    Route::get('/tasks/{ticket}', [TaskController::class, 'show'])->name('tasks.show');
    Route::delete('/tasks/{ticket}', [TaskController::class, 'destroy'])->name('tasks.destroy');
});

require __DIR__.'/auth.php';
