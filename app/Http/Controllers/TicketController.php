<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketLocation;
use App\Services\TelegramService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::query()->with(['user', 'category']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Priority Filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Category Filter
        if ($request->filled('category')) {
            $query->whereHas('category', function ($cat) use ($request) {
                $cat->where('name', $request->category);
            });
        }

        $user = Auth::user();
        if (!$user->can('view-any-tickets')) {
            $query->where('user_id', $user->id);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(10);
        $tickets->appends($request->query());

        return view('frontend.Tickets.tickets', [
            'tickets' => $tickets,
            'search' => $request->search,
            'filters' => $request->only(['status', 'priority', 'category']),
        ]);
    }



    public function create()
    {
        $categories = TicketCategory::where('is_active', true)->get();
        $locations = TicketLocation::where('is_active', true)->get();

        return view('frontend.Tickets.create', compact('categories', 'locations'));
    }



    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'priority' => 'required|string|in:Low,Medium,High',
                'category_id' => 'required|exists:ticket_categories,id',
                'location_id' => 'required|exists:ticket_locations,id',
            ]);

            $ticket = Ticket::create([
                'user_id' => auth()->id(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'priority' => $validated['priority'],
                'category_id' => $validated['category_id'],
                'location_id' => $validated['location_id'],
                'status' => 'Open',
            ]);

            // === Activity Log ===
        //    activity('ticket')
        //     ->performedOn($ticket)
        //     ->causedBy(auth()->user())
        //     ->withProperties($validated)
        //     ->log('Ticket dibuat');

            // Telegram notification
            $ticket->load(['category', 'location']);
            $telegram = app(TelegramService::class);

            $message =
                "📩 <b>Ticket Baru Masuk</b>\n" .
                "Judul     : {$ticket->title}\n" .
                "Prioritas : {$ticket->priority}\n" .
                "Kategori  : {$ticket->category->name}\n" .
                "Lokasi    : {$ticket->location->name}\n" .
                'Dari      : ' . auth()->user()->name . "\n\n" .
                'Silakan mengecek detailnya pada sistem 😊';

            $telegram->sendMessage($message);

            return redirect()->route('tickets.index')->with('success', 'Ticket created successfully.');
        } catch (Exception $e) {
            Log::error('Error creating ticket: ' . $e->getMessage());
            return back()->withErrors('An error occurred while creating the ticket.');
        }
    }



    public function edit(Ticket $ticket)
    {
        $user = Auth::user();

        if ($ticket->user_id !== $user->id && !$user->can('edit-own-ticket')) {
            abort(403, 'Unauthorized action.');
        }

        $categories = TicketCategory::where('is_active', true)->get();
        $locations = TicketLocation::where('is_active', true)->get();

        return view('frontend.Tickets.edit', compact('ticket', 'categories', 'locations'));
    }



    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        $user = Auth::user();

        if ($ticket->user_id !== $user->id && !$user->can('edit-own-ticket')) {
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

            // === Activity Log ===
            // activity('ticket')
            //     ->performedOn($ticket)
            //     ->causedBy($user)
            //     ->withProperties($validated)
            //     ->log('Ticket diperbarui');

            return redirect()->route('tickets.index')->with('success', 'Ticket updated successfully.');
        } catch (Exception $e) {
            Log::error('Error updating ticket: ' . $e->getMessage());
            return back()->withErrors('Error updating ticket.');
        }
    }



    public function show(Ticket $ticket)
    {
        $user = Auth::user();

        if (!$user->can('view-any-tickets') && $ticket->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

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

        // === Activity Log ===
        // activity('ticket')
        //     ->performedOn($ticket)
        //     ->causedBy($user)
        //     ->log('Ticket dihapus');

        return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully.');
    }



    // IT Support start
    public function start(Request $request, Ticket $ticket): RedirectResponse
    {
        if (!auth()->user()->can('handle-ticket')) {
            abort(403, 'Unauthorized action.');
        }

        $ticket->update([
            'status' => 'In Progress',
            'started_at' => $ticket->started_at ?? now(),
            'assigned_to' => auth()->id(),
        ]);

        // // === Activity Log ===
        // activity('ticket')
        //     ->performedOn($ticket)
        //     ->causedBy(auth()->user())
        //     ->withProperties(['status' => 'In Progress'])
        //     ->log('Ticket mulai ditangani IT Support');

        return redirect()->route('tickets.index')->with('success', 'Ticket started successfully.');
    }



    public function takeOver($id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($ticket->status === 'In Progress' && $ticket->assigned_to !== auth()->id()) {
            if (auth()->user()->can('take-over')) {
                $ticket->update([
                    'assigned_to' => auth()->id(),
                ]);

                // === Activity Log ===
                // activity('ticket')
                //     ->performedOn($ticket)
                //     ->causedBy(auth()->user())
                //     ->withProperties(['assigned_to' => auth()->id()])
                //     ->log('Ticket diambil alih oleh IT Support lain');

                return redirect()->route('tickets.index')->with('success', 'You have taken over the ticket.');
            }
        }
    }



    public function close(Request $request, Ticket $ticket): RedirectResponse
    {
        if (!auth()->user()->can('close-ticket')) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'solution' => 'required|string|min:1',
        ]);

        $start = $ticket->started_at ?: $ticket->created_at;
        $duration = $start->diffInMinutes(now());

        $ticket->update([
            'solution' => $data['solution'],
            'status' => 'Closed',
            'solved_by' => auth()->id(),
            'solved_at' => now(),
            'duration' => $duration,
        ]);

        // === Activity Log ===
        // activity('ticket')
        //     ->performedOn($ticket)
        //     ->causedBy(auth()->user())
        //     ->withProperties([
        //         'solution' => $data['solution'],
        //         'duration' => $duration,
        //     ])
        //     ->log('Ticket ditutup');

        return redirect()->route('tickets.index')->with('success', 'Ticket closed successfully.');
    }



    public function escalate(Request $request, Ticket $ticket): RedirectResponse
    {
        $user = Auth::user();

        if (!$user->can('escalate-ticket')) {
            abort(403, 'Unauthorized action.');
        }

        if ($ticket->assigned_to !== $user->id) {
            return back()->with('error', 'You cannot escalate this ticket.');
        }

        $ticket->update([
            'is_escalation' => true,
            'escalated_at' => now(),
            'status' => 'In Progress',
            'assigned_to' => null,
        ]);

        // === Activity Log ===
        // activity('ticket')
        //     ->performedOn($ticket)
        //     ->causedBy($user)
        //     ->withProperties(['is_escalation' => true])
        //     ->log('Ticket dieskalasi ke Admin');

        // Telegram
        try {
            $ticket->load(['category', 'location', 'user']);
            $telegram = app(TelegramService::class);

            $message =
                "⚠️ <b>Ticket DIESKALASIKAN</b>\n" .
                "Judul     : {$ticket->title}\n" .
                "Prioritas : {$ticket->priority}\n" .
                "Dari      : {$ticket->user->name}\n" .
                'Eskalasi Oleh: ' . $user->name . "\n\n" .
                '🛠️ <i>Tiket ini memerlukan perhatian admin untuk tindak lanjut.</i>';

            $telegram->sendMessage($message);
        } catch (Exception $e) {
            Log::error('Gagal mengirim notifikasi Telegram: ' . $e->getMessage());
        }

        return redirect()->route('tickets.index')->with('success', 'Ticket escalated and notification sent.');
    }



    public function handleEscalated(Ticket $ticket): RedirectResponse
    {
        $user = Auth::user();

        $ticket->update([
            'status' => 'In Progress',
            'assigned_to' => $user->id,
            'is_escalation' => false,
        ]);

        // === Activity Log ===
        // activity('ticket')
        //     ->performedOn($ticket)
        //     ->causedBy($user)
        //     ->withProperties(['assigned_to' => $user->id])
        //     ->log('Admin menangani tiket eskalasi');

        return redirect()->route('tickets.index')->with('success', 'Escalated ticket now handled by Admin.');
    }



    public function cancel(Ticket $ticket)
    {
        if (auth()->id() !== $ticket->assigned_to) {
            abort(403, 'You are not authorized to cancel this ticket.');
        }

        $ticket->update([
            'status' => 'Open',
            'assigned_to' => null,
        ]);

        // === Activity Log ===
        // activity('ticket')
        //     ->performedOn($ticket)
        //     ->causedBy(auth()->user())
        //     ->withProperties(['status' => 'Open'])
        //     ->log('Ticket dibatalkan oleh IT Support & dibuka kembali');

        return redirect()->route('tickets.index')->with('success', 'Ticket has been released.');
    }
}
