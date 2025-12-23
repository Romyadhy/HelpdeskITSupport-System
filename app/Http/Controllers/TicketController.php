<?php

namespace App\Http\Controllers;

use App\Helpers\logActivity;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketLocation;
use App\Models\TicketNote;
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
                $q->where('title', 'like', "%$search%")->orWhere('description', 'like', "%$search%");
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
        if (! $user->can('view-any-tickets')) {
            $query->where('user_id', $user->id);
        }

        // Sort by date filter
        if ($request->sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $tickets = $query->paginate(10);
        $tickets->appends($request->query());

        // Get categories and locations for modals
        $categories = TicketCategory::where('is_active', true)->get();
        $locations = TicketLocation::where('is_active', true)->get();

        return view('frontend.Tickets.tickets', [
            'tickets' => $tickets,
            'search' => $request->search,
            'filters' => $request->only(['status', 'priority', 'category', 'sort']),
            'categories' => $categories,
            'locations' => $locations,
        ]);
    }



    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:ticket_categories,id',
                'location_id' => 'required|exists:ticket_locations,id',
                'priority' => 'required|in:Low,Medium,High',
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

            // Log
            logActivity::add('ticket', 'created', $ticket, 'Ticket dibuat', [
                'new' => [
                    'title' => $ticket->title,
                    'priority' => $ticket->priority,
                    'status' => $ticket->status,
                ],
            ]);

            // Telegram notification
            $ticket->load(['category', 'location']);
            $telegram = app(TelegramService::class);

            $message = "📩 <b>Ticket Baru Masuk</b>\n" .
                "Judul     : {$ticket->title}\n" .
                "Prioritas Saat ini : {$ticket->priority}\n" .
                "Kategori  : {$ticket->category->name}\n" .
                "Lokasi    : {$ticket->location->name}\n" .
                'Dari      : ' . auth()->user()->name . "\n\n" .
                '⚠️ Silakan admin menentukan prioritas untuk ticket ini.';

            $telegram->sendMessage($message);

            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ticket created successfully.',
                    'ticket' => $ticket
                ], 201);
            }

            return redirect()->route('tickets.index')->with('success', 'Ticket created successfully.');
        } catch (Exception $e) {
            Log::error('Error creating ticket: ' . $e->getMessage());

            // Return JSON error for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while creating the ticket.'
                ], 500);
            }

            return back()->withErrors('An error occurred while creating the ticket.');
        }
    }



    public function update(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        if ($ticket->user_id !== $user->id && ! $user->can('edit-own-ticket')) {
            // Return JSON error for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'priority' => 'nullable|string|in:Low,Medium,High',
                'category_id' => 'required|exists:ticket_categories,id',
                'location_id' => 'required|exists:ticket_locations,id',
            ]);

            // old value
            $old = $ticket->only(['title', 'description', 'priority', 'category_id', 'location_id', 'status']);

            $ticket->update($validated);

            $new = $ticket->only(['title', 'description', 'priority', 'category_id', 'location_id', 'status']);

            // Log
            logActivity::add('ticket', 'updated', $ticket, 'Ticket diperbarui', [
                'old' => $old,
                'new' => $new,
            ]);

            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ticket updated successfully.',
                    'ticket' => $ticket
                ], 200);
            }

            return redirect()->route('tickets.index')->with('success', 'Ticket updated successfully.');
        } catch (Exception $e) {
            Log::error('Error updating ticket: ' . $e->getMessage());

            // Return JSON error for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating ticket.'
                ], 500);
            }

            return back()->withErrors('Error updating ticket.');
        }
    }

    public function show(Ticket $ticket)
    {
        $user = Auth::user();

        if (! $user->can('view-any-tickets') && $ticket->user_id !== $user->id) {
            // Return JSON error for AJAX requests
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        $categoryName = TicketCategory::find($ticket->category_id)->name;
        $locationName = TicketLocation::find($ticket->location_id)->name;

        // Return JSON for AJAX requests
        if (request()->expectsJson() || request()->ajax()) {
            $ticket->load(['user', 'assignee', 'solver', 'notes.user']);

            //durasi ticket mulai di kerjakan
            $waitingDuration = null;

            //durasi ticket di kerjakan sampai selesai
            $progressDuration = null;

            //durasi total ticket dari di buat sampai selesai
            $totalDuration = null;

            if ($ticket->started_at) {
                $waitingDuration = $ticket->waiting_duration_human;
            }

            if ($ticket->started_at) {
                if ($ticket->solved_at) {
                    $progressDuration = $ticket->progress_duration_human;
                } else {
                    // Live progress (sedang dikerjakan)
                    $minutes = $ticket->started_at->diffInMinutes(now());
                    $interval = \Carbon\CarbonInterval::minutes($minutes)->cascade();
                    $progressDuration =
                        ($interval->hours ? $interval->hours . 'h ' : '') .
                        $interval->minutes . 'm (running)';
                }
            }

            if ($ticket->solved_at) {
                $totalDuration = $ticket->total_duration_human;
            }

            return response()->json([
                'id' => $ticket->id,
                'title' => $ticket->title,
                'description' => $ticket->description,
                'status' => $ticket->status,
                'priority' => $ticket->priority,

                'waiting_duration' => $waitingDuration,
                'progress_duration' => $progressDuration,
                'total_duration' => $totalDuration,

                'category' => $categoryName,
                'location' => $locationName,
                'user' => $ticket->user->name,
                'assigned_to' => $ticket->assignee ? $ticket->assignee->name : null,
                'closed_by' => $ticket->solver ? $ticket->solver->name : null,
                'solution' => $ticket->solution,
                'solution_image_url' => $ticket->solution_image_url,

                'notes' => $ticket->notes->map(function ($note) {
                    return [
                        'id' => $note->id,
                        'note' => $note->note,
                        'author' => $note->user ? $note->user->name : 'Unknown',
                        'created_at' => $note->created_at
                            ->setTimezone('Asia/Makassar')
                            ->translatedFormat('d M Y, H:i') . ' WITA',
                    ];
                }),


                'created_at' => $ticket->created_at->setTimezone('Asia/Makassar')->translatedFormat('d M Y, H:i') . ' WITA',
                'updated_at' => $ticket->updated_at->setTimezone('Asia/Makassar')->translatedFormat('d M Y, H:i') . ' WITA',
                'started_at' => $ticket->started_at ? $ticket->started_at->setTimezone('Asia/Makassar')->translatedFormat('d M Y, H:i') . ' WITA' : null,
                'solved_at' => $ticket->solved_at ? $ticket->solved_at->setTimezone('Asia/Makassar')->translatedFormat('d M Y, H:i') . ' WITA' : null,
            ]);
        }

        // return view('frontend.Tickets.show', compact('ticket', 'categoryName', 'locationName'));
    }

    public function destroy(Ticket $ticket)
    {
        $user = Auth::user();

        if ($ticket->user_id !== $user->id && ! $user->can('delete-own-ticket')) {
            abort(403, 'Unauthorized action.');
        }

        $ticket->delete();

        // Log
        logActivity::add('ticket', 'deleted', $ticket, 'Ticket dihapus', [
            'old' => $ticket->getOriginal(),
        ]);

        return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully.');
    }

    // IT Support start
    public function start(Request $request, Ticket $ticket): RedirectResponse
    {
        if (! auth()->user()->can('handle-ticket')) {
            abort(403, 'Unauthorized action.');
        }

        $ticket->update([
            'status' => 'In Progress',
            'started_at' => $ticket->started_at ?? now(),
            'assigned_to' => auth()->id(),
        ]);

        // Log
        logActivity::add('ticket', 'start', $ticket, 'Ticket mulai ditangani', [
            'old' => [
                'status' => 'Open',
                'assigned_to' => null,
            ],
            'new' => [
                'status' => 'In Progress',
                'assigned_to' => auth()->id(),
            ],
        ]);

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

                // Log
                logActivity::add('ticket', 'takeover', $ticket, 'Ticket di take over', [
                    'old' => [
                        'assigned_to' => $ticket->assigned_to,
                        'status' => $ticket->status,
                    ],
                    'new' => [
                        'assigned_to' => auth()->id(),
                        'status' => $ticket->status,
                    ],
                ]);

                return redirect()->route('tickets.index')->with('success', 'You have taken over the ticket.');
            }
        }
    }

    public function close(Request $request, Ticket $ticket): RedirectResponse
    {
        if (! auth()->user()->can('close-ticket')) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'solution' => 'required|string|min:1',
            'solution_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imageName = null;

        if ($request->hasFile('solution_image')) {
            $imageName = uniqid() . '.' . $request->solution_image->extension();
            $request->solution_image->storedAs(
                'ticket-solution',
                $imageName,
                'public'
            );
        }

        $start = $ticket->started_at ?: $ticket->created_at;
        $duration = $start->diffInMinutes(now());

        $ticket->update([
            'solution' => $data['solution'],
            'solution_image' => $imageName,
            'status' => 'Closed',
            'solved_by' => auth()->id(),
            'solved_at' => now(),
            'duration' => $duration,
        ]);

        // Log
        logActivity::add('ticket', 'close', $ticket, 'Ticket diselesaikan', [
            'new' => [
                'status' => 'Closed',
                'solution' => $ticket->solution,
                'solved_by' => auth()->id(),
                'duration' => $duration,
            ],
        ]);

        return redirect()->route('tickets.index')->with('success', 'Ticket closed successfully.');
    }

    public function escalate(Request $request, Ticket $ticket): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->can('escalate-ticket')) {
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

        // Log
        logActivity::add('ticket', 'escalate', $ticket, 'Ticket dieskalasi', [
            'old' => [
                'is_escalation' => false,
                'status' => 'In Progress',
                'assigned_to' => $ticket->assigned_to,
            ],
            'new' => [
                'is_escalation' => true,
                'status' => 'In Progress',
                'assigned_to' => null,
            ],
        ]);

        // Telegram
        try {
            $ticket->load(['category', 'location', 'user']);
            $telegram = app(TelegramService::class);

            $message = "⚠️ <b>Ticket DIESKALASIKAN</b>\n" . "Judul     : {$ticket->title}\n" . "Prioritas : {$ticket->priority}\n" . "Dari      : {$ticket->user->name}\n" . 'Eskalasi Oleh: ' . $user->name . "\n\n" . '🛠️ <i>Tiket ini memerlukan perhatian admin untuk tindak lanjut.</i>';

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

        // Log
        logActivity::add('ticket', 'handle-escalated', $ticket, 'Ticket eskalasi ditangani oleh Admin', [
            'old' => [
                'is_escalation' => true,
                'assigned_to' => null,
            ],
            'new' => [
                'is_escalation' => false,
                'assigned_to' => $user->id,
            ],
        ]);

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

        // Log
        logActivity::add('ticket', 'cancel', $ticket, 'Ticket ditunda', [
            'old' => [
                'status' => 'In Progress',
                'assigned_to' => $ticket->assigned_to,
            ],
            'new' => [
                'status' => 'Open',
                'assigned_to' => null,
            ],
        ]);

        return redirect()->route('tickets.index')->with('success', 'Ticket has been released.');
    }

    //set priority
    public function setPriority(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'priority' => 'required|string|in:Low,Medium,High',
        ]);

        $oldPriority = $ticket->priority;

        $ticket->update(['priority' => $validated['priority']]);

        logActivity::add('ticket', 'set-priority', $ticket, 'Prioritas ticket ditetapkan', [
            'old' => ['priority' => $oldPriority],
            'new' => ['priority' => $validated['priority']],
        ]);

        try {
            // load relasi
            $ticket->load(['category', 'location', 'user']);
            $telegram = app(TelegramService::class);

            $categoryName = TicketCategory::find($ticket->category_id)->name;
            $locationName = TicketLocation::find($ticket->location_id)->name;
            $userName     = $ticket->user?->name ?? auth()->user()->name;

            $priorityEmoji = [
                'High' => '🔴',
                'Medium' => '🟡',
                'Low' => '🟢',
            ];

            $emoji = $priorityEmoji[$validated['priority']] ?? '⚪';

            $message = "{$emoji} <b>Prioritas Ticket Ditetapkan</b>\n" .
                "Ticket ID : #{$ticket->id}\n" .
                "Judul     : {$ticket->title}\n" .
                "Prioritas : {$validated['priority']}\n" .
                "Kategori  : {$categoryName}\n" .
                "Lokasi    : {$locationName}\n" .
                "Ditetapkan oleh: {$userName}\n\n" .
                "📌 Ticket siap ditangani sesuai prioritas.";

            $telegram->sendMessage($message);
        } catch (Exception $e) {
            Log::error('Gagal mengirim notifikasi Telegram: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Priority set successfully.',
            'priority' => $validated['priority'],
        ]);
    }


    public function storeNote(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        // Hanya admin (atau role yang punya ability ini) yang boleh tambah note
        if (! $user->can('view-any-tickets')) { // silakan sesuaikan dengan policy kamu
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        // Hanya boleh tambah note kalau statusnya Open
        if ($ticket->status !== 'Open') {
            return response()->json([
                'success' => false,
                'message' => 'Notes hanya dapat ditambahkan ketika ticket berstatus Open.',
            ], 422);
        }

        $validated = $request->validate([
            'note' => ['required', 'string', 'max:2000'],
        ]);

        $note = TicketNote::create([
            'ticket_id' => $ticket->id,
            'user_id'   => $user->id,
            'note'      => $validated['note'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Note berhasil ditambahkan.',
            'note' => [
                'id' => $note->id,
                'note' => $note->note,
                'author' => $note->user->name,
                'created_at' => $note->created_at
                    ->setTimezone('Asia/Makassar')
                    ->translatedFormat('d M Y, H:i') . ' WITA',
            ],
        ]);
    }
}
