<?php

namespace App\Livewire\Handbook;

use App\Http\Controllers\HandbookController;
use App\Models\Handbook;
use Livewire\Component;

class ShowHandBook extends Component
{
    /* public Handbook $handbook; */
    /**/
    /* public function mount(Handbook $handbook) */
    /* { */
    /*     $this->handbook = $handbook; */
    /* } */
    /**/
    /* public function render() */
    /* { */
    /*     $controller = app(HandbookController::class); */
    /**/
    /*     return $controller->show($this->handbook); */
    /* } */
    /* public function render($id) */
    /* { */
    /*     $handbook = Handbook::with('uploader')->findOrFail($id); */
    /**/
    /*     // dd($handbook); */
    /*     return view('frontend.Handbook.show', compact('handbook'))->layout('layouts.app'); */
    /* } */
    public $Id;

    public function mount($Id)
    {
        $this->Id = $Id;
    }

    public function render()
    {
        $controller = app(HandbookController::class);
        $handbook = Handbook::findOrFail($this->Id);

        return $controller->show($handbook->id);
    }
}
