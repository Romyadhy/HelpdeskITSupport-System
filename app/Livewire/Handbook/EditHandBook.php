<?php

namespace App\Livewire\Handbook;

use App\Http\Controllers\HandbookController;
use App\Models\Handbook;
use Livewire\Component;

class EditHandBook extends Component
{
    public $Id;

    public function mount($Id)
    {
        $this->Id = $Id;
    }

    public function render()
    {
        $controller = app(HandbookController::class);
        $handbook = Handbook::findOrFail($this->Id);

        return $controller->edit($handbook->id);
    }
}
