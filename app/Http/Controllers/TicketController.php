<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Policies\TicketPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    public function index()
        {
            $users = Auth::user();
            $ticketsQuery = Ticket::with('user')->latest();

            if ($users->can('view-any-tickets')) {
                $tickets = $ticketsQuery->paginate(10);
            } else {
                $tickets = $ticketsQuery->where('user_id', $users->id)->paginate(10);
            }
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

            } catch (Exception $e) {
                \Log::error('Error creating ticket: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
                return back()->withErrors('An error occurred while creating the ticket. Please try again.');
            }
            return back()->withErrors('error','Something went wrong while creating the ticket.');        
        }

    public function edit(Ticket $ticket)
        {
            $user = Auth::user();

           if($ticket->user_id !== $user->id && !$user->can('edit-own-ticket')) {
                abort(403, 'Unauthorized action.');
            }

            $categories = TicketCategory::where('is_active', true)->get();
            $locations = TicketLocation::where('is_active', true)->get();

            return view('frontend.Tickets.edit', compact('ticket', 'categories', 'locations'));
        }

    public function update(Request $request, Ticket $ticket): RedirectResponse
        {
            $user = Auth::user();
            
            if($ticket->user_id !== $user->id && !$user->can('edit-own-ticket')) {
                abort(403, 'Unauthorized action.');
            }

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
            } catch (Exception $e) {
                \Log::error('Error updating ticket: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
                return back()->withErrors('error', 'An error occurred while updating the ticket.');
            }
        }
    
    public function show(Ticket $ticket)
        {
            $user = Auth::user();

            if (!$user->can('view-any-tickets') && $ticket->user_id !== $user->id) {
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

            return view('frontend.Tickets.show', compact('ticket', 'categoryName', 'locationName'));
        }

    public function destroy(Ticket $ticket)
        {
            $user = Auth::user();

            if ($ticket->user_id !== $user->id && !$user->can('delete-own-ticket')) {
                abort(403, 'Unauthorized action.');
            }

            $ticket->delete();
            return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully.');
        }

    // POV IT Support
    public function start(Request $request, Ticket $ticket): RedirectResponse
        {
            $user = Auth::user();

            if(!$user->can('handle-ticket')){
                abort(403, 'Unauthorized action.');
            }
            try {
                $ticket->update([
                    'status' => 'In Progress',
                    'started_at' => $ticket->started_at ?? now(),
                    'assigned_to' => auth()->id(),
                    // 'updated_at' => now(),
                ]);
                return redirect()->route('tickets.index')->with('success', 'Ticket started successfully.');
            } catch (Exception $e) {
                \Log::error('Error starting ticket: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
                return back()->withErrors('error', 'An error occurred while starting the ticket.');
            }
        }

    public function takeOver($id)
    {
        $ticket = Ticket::findOrFail($id);

        // check ticket status
        if ($ticket->status === 'In Progress' && $ticket->assigned_to !== auth()->id()){
            if (auth()->user()->can('take-over')) {
                $ticket->save();
                $ticket->update([
                    'assigned_to' => auth()->id(),
                ]);
                return redirect()->route('tickets.index')->with('success', 'You have taken over the ticket.');
            }
        }
    }

    public function close(Request $request, Ticket $ticket): RedirectResponse
        {
            $user = Auth::user();

            if (!$user->can('close-ticket')) {
                abort(403, 'Unauthorized action.');
            }

            try {
                $data = $request->validate([
                    'solution' => 'required|string|min:1',
                ]);

                $start    = $ticket->started_at ?: $ticket->created_at; // fallback ke created_at
                $closedAt = now();

                // gunakan $start->diffInMinutes($closedAt) agar hasil pasti positif
                $duration = $start->diffInMinutes($closedAt);

                $ticket->update([
                    'solution' => $data['solution'],
                    'status' => 'Closed',
                    'solved_by' => auth()->id(),
                    'solved_at' => now(),
                    'duration' => $duration,
                ]);
                return redirect()->route('tickets.index')->with('success', 'Ticket closed successfully.');

            } catch (Exception $e) {
                \Log::error('Error closing ticket: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
                return back()->withErrors('Error', 'An error occurred while closing the ticket.');
            }
        }

    public function escalate(Request $request, Ticket $ticket): RedirectResponse
    {
        $user = Auth::user();

        if (!$user->can('escalate-ticket')) {
            abort(403, 'Unauthorized action.');
        }

        if ($ticket->assigned_to === $user->id) {
            $ticket->update([
                'is_escalation' => true,
                'escalated_at'  => now(),
                'status'        => 'In Progress',
                'assigned_to'   => null,
            ]);
            return redirect()->route('tickets.index')->with('success', 'Ticket has been escalated to Admin.');
        }

        return back()->with('error', 'You cannot escalate this ticket.');
    }


    // Admin menangani tiket yang di-escalate
    public function handleEscalated(Ticket $ticket): RedirectResponse
    {
        $user = Auth::user();

        // if (!$user->can('handle-escalated-ticket')) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $ticket->update([
                'status' => 'In Progress',
                'assigned_to' => $user->id,
                'is_escalation' => false,
            ]);

            return redirect()->route('tickets.index')->with('success', 'Escalated ticket now handled by Admin.');
        } catch (Exception $e) {
            Log::error('Error handling escalated ticket: ' . $e->getMessage());
            return back()->withErrors('An error occurred while handling the escalated ticket.');
        }
    }
}





