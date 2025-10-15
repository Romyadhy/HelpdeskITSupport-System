<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskCompletion;
use Carbon\Month;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;

class TaskController extends Controller
{
    public function index()
    {
        // Logic to list tasks
        // $dailyTasks = Task::where('frequency', 'daily')->get();
        // $monthlyTasks = Task::where('frequency', 'monthly')->get();

        // return view('frontend.Tasks.index', compact('dailyTasks', 'monthlyTasks'));

        Gate::authorize('view-tasks');

        $dailyTasks = Task::where('frequency', 'daily')->get();
        $monthlyTasks = Task::where('frequency', 'monthly')->get();

        $completedTodayIds = TaskCompletion::where('user_id', auth()->id())
            ->whereDate('complated_at', today())
            ->pluck('task_id')
            ->toArray();
        
        $complatedMonthly = TaskCompletion::where('user_id', auth()->id())
            // ->whereDate('complate_at', monthly())
            ->whereMonth('complated_at', now()->month)
            ->whereYear('complated_at', now()->year)
            ->pluck('task_id')
            ->toArray();

        return view('frontend.Tasks.index', compact('dailyTasks', 'monthlyTasks',  'completedTodayIds', 'complatedMonthly'));
    }

    public function show(Task $task)
    {
        // logic to show detail
        // $titletes = Task::all();
        // dd($titletes);
        // return view('frontend.Tasks.show', compact('task'));
        Gate::authorize('view-tasks');

        // Ambil semua completion untuk task ini
        $completions = $task->completions()
            ->with('user')
            ->orderByDesc('complated_at')
            ->get();

        // Hitung berapa kali diselesaikan bulan ini
        $completedCountThisMonth = $task->completions()
            ->whereMonth('complated_at', now()->month)
            ->whereYear('complated_at', now()->year)
            ->count();
        
        // dd($completedCountThisMonth);

        return view('frontend.Tasks.show', compact('task', 'completions', 'completedCountThisMonth'));
    }

    public function create()
    {
        // Logic to show form to create a task
        return view('frontend.Tasks.create');
    }

    public function store(Request $request)
    {
        // Logic to store a new task
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'frequency' => 'required|in:Daily,Monthly',
                // 'is_active' => 'nullable'
            ]);

            Task::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'frequency' => $validated['frequency'],
                // 'is_active' => $validated['is_active'],
            ]);

            return redirect()->route('tasks.index')->with('success', 'Task created successfully');
        } catch (Exception $e) {
            \Log::error('Eror creating ticket: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return back()->withErrors('An error occurred while creating the task.');
        }
        return back()->withErrors('error', 'Something went wrong while creating the task');
    }

    public function edit(Task $task)
    {
        // Logic to show form to edit a task
        return view('frontend.Tasks.edit', compact('task'));
    }

    public function update(Request $request, $id)
    {
        // Logic to update a task
    }

    public function destroy($id)
    {
        // Logic to delete a task
    }

    public function complete(Request $request, Task $task)
    {
        // Gate::authorize('checked-task');
        Gate::define('checked-task', function ($user) {
            return in_array($user->role, ['admin', 'support', 'manager']);
        });

        $alreadyDone = TaskCompletion::where('task_id', $task->id)
            ->where('user_id', auth()->id())
            ->whereDate('complated_at', today())
            ->exists();

        if ($alreadyDone) {
            return back()->with('error', 'task is already complated');
        }

        TaskCompletion::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'complated_at' => now(),
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Task Complated');
    }
}
