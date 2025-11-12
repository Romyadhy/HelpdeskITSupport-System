<?php

namespace App\Livewire\Handbook;

use App\Http\Controllers\HandbookController;
use App\Models\Handbook;
use Livewire\Component;

class IndexHandbook extends Component
{
    public function render()
    {
        // $controller = app(HandbookController::class);
        // return $controller->index();

        $handbooks = Handbook::with('uploader')->latest()->get();
        return view('frontend.Handbook.index', compact('handbooks'))->layout('layouts.app');
    }
}
