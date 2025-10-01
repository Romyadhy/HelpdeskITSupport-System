<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('user')->latest()->get();
        return view('frontend.Tickets.tickets', ['tickets' => $tickets]);
    }

    public function create()
    {
        return view('frontend.Tickets.create');
    }

    public function store(Request $request) :RedirectResponse
    {

        try {
            $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|string|in:Low,Medium,High',
            'status' => 'nullable|string|in:Open,Closed,In Progress',
            'category' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',

        ]);

         Ticket::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'category' => $validated['category'] ?? null,
            'location' => $validated['location'] ?? null,
            'status' => 'Open',
        ]);

        return redirect()->route('tickets.index')->with('success', 'Ticket created successfully.');

        } catch (\Exception $e) {
            \Log::error('Error creating ticket: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return back()->withErrors('An error occurred while creating the ticket. Please try again.');
        }
        return back()->withErrors('error','Something went wrong while creating the ticket.');        
    }

    public function close(Request $reqest, Ticket $ticket): RedirectResponse
    {
        try {
            $data = $reqest->validate([
                'solution' => 'required|string|min:1',
            ]);

            $ticket->update([
                'solution' => $data['solution'],
                'status' => 'Closed',
                'solved_by' => auth()->id(),
                'solved_at' => now(),
                'duration' => $ticket->started_at 
                                ? now()->diffInMinutes($ticket->started_at) 
                                : now()->diffInMinutes($ticket->created_at),
            ]);
            return redirect()->route('tickets.index')->with('success', 'Ticket closed successfully.');

        } catch (\Exception $e) {
            \Log::error('Error closing ticket: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return back()->withErrors('Error', 'An error occurred while closing the ticket.');
        }
    }

    public function edit(Ticket $ticket)
    {
        if (!auth()->user()->isAdmin() && $ticket->user_id !== auth()->id()) {
        abort(403, 'Unauthorized action.');
    }

    return view('frontend.Tickets.edit', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
         if (!auth()->user()->isAdmin() && $ticket->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'priority' => 'required|string|in:Low,Medium,High',
                'category' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
            ]);

            $ticket->update($validated);

            return redirect()->route('tickets.index')->with('success', 'Ticket updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating ticket: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return back()->withErrors('error', 'An error occurred while updating the ticket.');
        }
    }

    public function destroy(Ticket $ticket)
    {
        if (!auth()->user()->isAdmin() && $ticket->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully.');
    }
}
