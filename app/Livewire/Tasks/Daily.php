<?php

namespace App\Livewire\Tasks;

use App\Http\Controllers\TaskController;
use Livewire\Component;

class Daily extends Component
{
    public function render()
    {
        $controller = app(TaskController::class);
        return $controller->daily();
    }
}
