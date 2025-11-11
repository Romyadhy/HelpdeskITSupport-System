<?php

namespace App\Livewire\Tickets;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketLocation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EditPage extends Component
{
    public function render()
    {
        $user = Auth::user();
        $ticket = Ticket::with(['category', 'location', 'user', 'assignee'])->findOrFail(request()->route('ticket'));

        if ($ticket->user_id !== $user->id && ! $user->can('edit-own-ticket')) {
            abort(403, 'Unauthorized action.');
        }

        $categories = TicketCategory::where('is_active', true)->get();
        $locations = TicketLocation::where('is_active', true)->get();

        return view('frontend.Tickets.edit', compact('ticket', 'categories', 'locations'))->layout('layouts.app');
    }
}
