<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;

class TaskController extends Controller
{
    public function index()
    {
        // Logic to list tasks
        $dailyTasks = Task::where('frequency', 'daily')->get();
        $monthlyTasks = Task::where('frequency', 'monthly')->get();

        return view('frontend.Tasks.index', compact('dailyTasks', 'monthlyTasks'));
    }

    public function show(Task $task)
    {
        // logic to show detail 
        $titletes = Task::find('title');
        dd($titletes);
        return view('frontend.Tasks.show', compact('task'));
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
                'frequency'  => 'required|in:Daily,Monthly',
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
            \Log::error('Eror creating ticket: '. $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
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
}
