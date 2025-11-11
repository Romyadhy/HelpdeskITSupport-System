<?php

namespace App\Livewire\Tickets;

use App\Http\Controllers\TicketController;
use Livewire\Component;

class CreatePage extends Component
{
    public function render()
    {
        $controller = app(TicketController::class);
        return $controller->create();
    }
}
