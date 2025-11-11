<?php

namespace App\Livewire\Tasks;

use App\Http\Controllers\TaskController;
use Livewire\Component;

class Create extends Component
{
    public function render()
    {
        $controller = app(TaskController::class);
        return $controller->create();
    }
}
