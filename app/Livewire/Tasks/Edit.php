<?php

namespace App\Livewire\Tasks;

use App\Http\Controllers\TaskController;
use App\Models\Task;
use Livewire\Component;

class Edit extends Component
{
    
    public $task;

    public function mount($task)
    {
        // $this->taskId = $taskId;
        $this->task = Task::findOrFail($task);
    }

    public function render(){
        
        $task = $this->task;
        $controller = app(TaskController::class);
        $controller->edit($task); 

        return view('frontend.Tasks.edit', compact('task'))->layout('layouts.app');
    }
}
