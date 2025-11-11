<?php

namespace App\Livewire\Tickets;
use App\Http\Controllers\TicketController;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketLocation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ShowPage extends Component
{
    
    public function render()
    {
        $user = Auth::user();
        $ticket = Ticket::with(['category', 'location', 'user', 'assignee'])->findOrFail(request()->route('ticket'));

        if (! $user->can('view-any-tickets') && $ticket->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        // dd($ticket);
        // dd($ticket->category->name);
        // dd($ticket->location->name);
        // $tes = $ticket->load(['category', 'location', 'user', 'solver']);
        // dd($tes);
        // dd($ticket->assignee->name);

        // calling manually
        $categoryName = TicketCategory::find($ticket->category_id)->name;
        $locationName = TicketLocation::find($ticket->location_id)->name;

        return view('frontend.Tickets.show', compact('ticket', 'categoryName', 'locationName'))->layout('layouts.app');
    }

}
