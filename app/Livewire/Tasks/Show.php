<?php

namespace App\Livewire\Tasks;

use App\Http\Controllers\TaskController;
use App\Models\Task;
use Livewire\Component;

class Show extends Component
{
    public Task $task;

    public function mount(Task $task)
    {
        $this->task = $task;
    }

    public function render()
    {
        $controller = app(TaskController::class);
        return $controller->show($this->task);
    }
}
 