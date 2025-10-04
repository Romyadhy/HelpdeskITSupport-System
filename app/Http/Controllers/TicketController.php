<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Policies\TicketPolicy;

class TicketController extends Controller
{
    public function index()
        {
            $tickets = Ticket::with('user', 'category', 'location')->latest()->get();
            return view('frontend.Tickets.tickets', ['tickets' => $tickets]);
        }

    public function create()
        {
            $categories = TicketCategory::where('is_active', true)->get();
            $locations = TicketLocation::where('is_active', true)->get();
            return view('frontend.Tickets.create' , compact('categories', 'locations'));
        }

    public function store(Request $request) :RedirectResponse
        {

            try {
                $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'priority' => 'required|string|in:Low,Medium,High',
                'status' => 'nullable|string|in:Open,Closed,In Progress',
                'category_id' => 'required|exists:ticket_categories,id',
                'location_id' => 'required|exists:ticket_locations,id',

            ]);

            Ticket::create([
                'user_id' => auth()->id(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'priority' => $validated['priority'],
                'category_id' => $validated['category_id'],
                'location_id' => $validated['location_id'],
                'status' => 'Open',
            ]);

            return redirect()->route('tickets.index')->with('success', 'Ticket created successfully.');

            } catch (\Exception $e) {
                \Log::error('Error creating ticket: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
                return back()->withErrors('An error occurred while creating the ticket. Please try again.');
            }
            return back()->withErrors('error','Something went wrong while creating the ticket.');        
        }

    public function edit(Ticket $ticket)
        {
            if (!auth()->user()->isAdmin() && $ticket->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
            }
            // $this->authorize('update', $ticket);

            $categories = TicketCategory::where('is_active', true)->get();
            $locations = TicketLocation::where('is_active', true)->get();

            return view('frontend.Tickets.edit', compact('ticket', 'categories', 'locations'));
        }

    public function update(Request $request, Ticket $ticket): RedirectResponse
        {
            if (!auth()->user()->isAdmin() && $ticket->user_id !== auth()->id()) {
                abort(403, 'Unauthorized action.');
            }
            // $this->authorize('update', $ticket);

            try {
                $validated = $request->validate([
                    'title' => 'required|string|max:255',
                    'description' => 'required|string',
                    'priority' => 'required|string|in:Low,Medium,High',
                    'category_id' => 'required|exists:ticket_categories,id',
                    'location_id' => 'required|exists:ticket_locations,id',
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

            // $this->authorize('delete', $ticket);

            $ticket->delete();

            return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully.');
        }

    // POV IT Support
    public function start(Request $request, Ticket $ticket): RedirectResponse
        {
            if (!auth()->user()->isSupport() && !auth()->user()->isAdmin()) {
                abort(403, 'Unauthorized action.');
            }

            try {
                $ticket->update([
                    'status' => 'In Progress',
                    'started_at' => now(),
                    'assigned_to' => auth()->id(),
                ]);
                return redirect()->route('tickets.index')->with('success', 'Ticket started successfully.');
            } catch (\Exception $e) {
                \Log::error('Error starting ticket: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
                return back()->withErrors('error', 'An error occurred while starting the ticket.');
            }
        }

    public function close(Request $request, Ticket $ticket): RedirectResponse
        {
            if (!auth()->user()->isSupport() && !auth()->user()->isAdmin()) {
                abort(403, 'Unauthorized action.');
            }
            try {
                $data = $request->validate([
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

    public function escalate(Request $request, Ticket $ticket): RedirectResponse
        {
            // dd("Escalated!", $ticket);

            if (!auth()->user()->isSupport()) {
                abort(403, 'Unauthorized action.');
            }

            try {
                $ticket->update([
                    'is_escalation' => true,
                    'escalated_at' => now(),
                ]);

                return redirect()->route('tickets.index')->with('success', 'Ticket escalated to Admin');

            } catch (\Exception $e) {
                \Log::error('Error fetching escalation tickets: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
                return back()->withErrors('Error', 'An error occurred while fetching escalation tickets.');
            }
        }
        
}





