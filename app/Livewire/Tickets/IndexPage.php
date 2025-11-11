<?php

namespace App\Livewire\Tickets;

use App\Http\Controllers\TicketController;
use Livewire\Component;

class IndexPage extends Component
{
    public function render()
    {
        $controller = app(TicketController::class);
        return $controller->index();
    }
}
