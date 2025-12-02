<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskCompletion;
use Carbon\Month;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Helpers\logActivity;

class TaskController extends Controller
{
    public function daily()
    {
        // $tasks = Task::where('frequency', 'daily')->orderBy('title')->get()->where('is_active', true);
        $isAdmin = auth()->user()->hasRole('admin'); // atau sesuaikan dengan sistem role kamu

        // Kalau admin, tampilkan semua task; kalau support, hanya yang aktif
        $tasksQuery = Task::where('frequency', 'daily')
            ->orderBy('title');

        if (! $isAdmin) {
            $tasksQuery->where('is_active', true);
        }

        $tasks = $tasksQuery->get();
        $completedTodays = TaskCompletion::whereDate('complated_at', today())
            ->pluck('task_id')
            ->unique()
            ->toArray();

        return view('frontend.Tasks.daily', compact('tasks', 'completedTodays'));
    }

    public function monthly()
    {
        $isAdmin = auth()->user()->hasRole('admin');

        $tasksQuery = Task::where('frequency', 'monthly')->orderBy('title');

        if (! $isAdmin) {
            $tasksQuery->where('is_active', true);
        }

        $tasks = $tasksQuery->get();
        $completedMonthlys = TaskCompletion::whereMonth('complated_at', now()->month)->whereYear('complated_at', now()->year)
            ->pluck('task_id')
            ->toArray();

        return view('frontend.Tasks.monthly', compact('tasks', 'completedMonthlys'));
    }

    public function show(Task $task)
    {
        Gate::authorize('view-tasks');

        $completions = $task->completions()->with('user')->orderByDesc('complated_at')->get();

        $completedCountThisMonth = $task->completions()->whereMonth('complated_at', now()->month)->whereYear('complated_at', now()->year)->count();

        // Return JSON for AJAX requests
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'frequency' => ucfirst($task->frequency),
                'is_active' => $task->is_active,
                'created_at' => $task->created_at->setTimezone('Asia/Makassar')->translatedFormat('d M Y, H:i') . ' WITA',
                'completed_count_this_month' => $completedCountThisMonth,
                'completions' => $completions->map(function($completion, $index) {
                    return [
                        'number' => $index + 1,
                        'user_name' => $completion->user->name ?? 'Unknown',
                        'completed_at' => $completion->complated_at->setTimezone('Asia/Makassar')->translatedFormat('d M Y, H:i') . ' WITA',
                        'notes' => $completion->notes
                    ];
                })
            ]);
        }

        // dd($task->title);
        // dd($task->toArray());

        return view('frontend.Tasks.show', compact('task', 'completions', 'completedCountThisMonth'));
    }

    public function create()
    {
        // Logic to show form to create a task
        return view('frontend.Tasks.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'frequency' => 'required|in:daily,monthly',
                'is_active' => 'nullable|boolean',
            ]);

            $task = Task::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'frequency' => $validated['frequency'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Log
            logActivity::add('task', 'created', $task, 'Task dibuat', [
                'new' => $task->toArray(),
            ]);

            // Return JSON for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Task created successfully',
                    'task' => $task
                ]);
            }

            return redirect()->route('tasks.daily')->with('success', 'Task created successfully');
        } catch (Exception $e) {
            \Log::error('Error creating task: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while creating the task.'
                ], 500);
            }

            return back()->withErrors('An error occurred while creating the task.');
        }
    }

    public function edit(Task $task)
    {
        // Logic to show form to edit a task
        return view('frontend.Tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        try {
            // Logic to update a task
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'frequency' => 'required|in:daily,monthly',
                'is_active' => 'nullable|boolean',
            ]);

            $old = $task->only(['title', 'description', 'frequency', 'is_active']);

            $task->update($validated);

            $new = $task->only(['title', 'description', 'frequency', 'is_active']);

            // Log
            logActivity::add('task', 'updated', $task, 'Task diperbarui', [
                'old' => $old,
                'new' => $new,
            ]);

            // Return JSON for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Task updated successfully',
                    'task' => $task
                ]);
            }

            return redirect()->route('tasks.show', $task->id)->with('success', 'Task updated successfully!');
        } catch (Exception $e) {
            \Log::error('Error updating task: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the task.',
                    'errors' => $e->getMessage()
                ], 500);
            }

            return back()->withErrors('An error occurred while updating the task.');
        }
    }

    public function destroy(Task $task)
    {
        // Logic to delete a task
        $old = $task->toArray();
        $task->delete();

        // Log
        logActivity::add('task', 'deleted', $task, 'Task dihapus', [
            'old' => $old,
        ]);

        return redirect()->route('tasks.daily')->with('success', 'Task deleted successfully!');
    }

    public function complete(Request $request, Task $task)
    {
        $alreadyDone = TaskCompletion::where('task_id', $task->id)
            ->where('user_id', auth()->id())
            ->whereDate('complated_at', today())
            ->exists();

        if ($alreadyDone) {
            return back()->with('error', 'task is already complated');
        }

        $completion = TaskCompletion::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'complated_at' => now(),
            'notes' => $request->notes,
        ]);

        // Log
        logActivity::add('task', 'completed', $task, 'Task diselesaikan', [
            'new' => [
                'task_title' => $task->title,
                'completion_id' => $completion->id,
                // 'notes' => $completion->notes,
                'completed_at' => $completion->complated_at,
                'completed_by' => auth()->user()->name,
            ],

            'old' => [
                'task_title' => $task->title,
                'task_frequency' => $task->frequency,
            ],
        ]);

        return back()->with('success', 'Task Complated');
    }
}
