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
        // validate the data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|string|in:Low,Medium,High',
            'status' => 'required|string|in:Open,Closed,In_Progress',
        ]);

        Ticket::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => 'Open',
        ]);

        return redirect()->route('tickets.index')->with('success', 'Ticket created successfully.');
    }
}
