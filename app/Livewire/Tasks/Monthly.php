<?php

namespace App\Livewire\Tasks;

use App\Http\Controllers\TaskController;
use Livewire\Component;

class Monthly extends Component
{
    public function render()
    {
        $controller = app(TaskController::class);
        return $controller->monthly();
    }
}
