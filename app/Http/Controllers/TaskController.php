<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        // Logic to list tasks
        $dailyTasks = Task::where('frequency', 'daily')->get();
        $monthlyTasks = Task::where('frequency', 'monthly')->get();

        return view('frontend.Tasks.index', compact('dailyTasks', 'monthlyTasks'));
    }

    public function show()
    {
        // logic to show detail 
    }

    public function create()
    {
        // Logic to show form to create a task
    }

    public function store(Request $request)
    {
        // Logic to store a new task
    }

    public function edit($id)
    {
        // Logic to show form to edit a task
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
