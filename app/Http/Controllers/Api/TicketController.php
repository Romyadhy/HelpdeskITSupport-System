<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    /**
     * 🔹 Get all tickets (index)
     */
    public function index(Request $request)
    {
        try {
            $tickets = Ticket::with([
                'user',
                'category',
                'location',
                'assignee',
                'solver',
            ])
                ->where('user_id', $request->user()->id)
                ->latest()
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Daftar tiket berhasil diambil.',
                'data' => $tickets,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * 🔹 Create a new ticket (store)
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'priority' => 'required|string|in:Low,Medium,High',
                'category_id' => 'required|exists:ticket_categories,id',
                'location_id' => 'nullable|exists:ticket_locations,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $ticket = Ticket::create([
                'title' => $request->title,
                'description' => $request->description,
                'priority' => $request->priority,
                'status' => 'Open',
                'user_id' => Auth::id(),
                'category_id' => $request->category_id,
                'location_id' => $request->location_id,
                'is_escalation' => false,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Tiket berhasil dibuat.',
                'data' => $ticket,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * 🔹 Show detail of a ticket
     */
    public function show($id)
    {
        try {
            $ticket = Ticket::with(['user', 'category', 'location', 'assignee', 'solver'])
                ->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Detail tiket berhasil diambil.',
                'data' => $ticket,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tiket tidak ditemukan atau terjadi kesalahan.',
                'error' => $th->getMessage(),
            ], 404);
        }
    }

    /**
     * 🔹 Update ticket
     */
    public function update(Request $request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'priority' => 'sometimes|string|in:Low,Medium,High',
                'status' => 'sometimes|string|in:Open,In Progress,Closed',
                'solution' => 'nullable|string',
                'assigned_to' => 'nullable|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $ticket->update($request->only([
                'title', 'description', 'priority', 'status',
                'solution', 'assigned_to',
            ]));

            return response()->json([
                'status' => 'success',
                'message' => 'Tiket berhasil diperbarui.',
                'data' => $ticket,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui tiket.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * 🔹 Delete ticket
     */
    public function destroy($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Tiket berhasil dihapus.',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus tiket.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * 🔹 Start handling a ticket (by IT Support)
     */
    public function startTicket(Request $request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $user = Auth::user();

            // Pastikan user memiliki hak untuk menangani tiket
            if (! $user->can('handle-ticket')) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Anda tidak memiliki izin untuk memulai tiket ini.',
                ], 403);
            }

            // Pastikan tiket belum ditangani atau sudah selesai
            if ($ticket->status !== 'Open') {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Tiket tidak dalam status Open.',
                ], 400);
            }

            $ticket->update([
                'status' => 'In Progress',
                'started_at' => $ticket->started_at ?? now(),
                'assigned_to' => $user->id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Tiket berhasil dimulai.',
                'data' => $ticket,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memulai tiket.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * 🔹 Close a ticket after solving it
     */
    public function closeTicket(Request $request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $user = Auth::user();

            // Pastikan hanya IT Support yang menangani tiket ini yang boleh menutupnya
            if ($ticket->assigned_to !== $user->id) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Anda tidak dapat menutup tiket ini.',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'solution' => 'required|string|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $start = $ticket->started_at ?: $ticket->created_at;
            $closedAt = now();
            $duration = $start->diffInMinutes($closedAt);

            $ticket->update([
                'solution' => $request->solution,
                'status' => 'Closed',
                'solved_by' => $user->id,
                'solved_at' => $closedAt,
                'duration' => $duration,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Tiket berhasil ditutup.',
                'data' => $ticket,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menutup tiket.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * 🔹 Escalate a ticket to Admin
     */
    public function escalateTicket(Request $request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $user = Auth::user();

            // Pastikan user memiliki izin untuk mengeskalasi tiket
            if (! $user->can('escalate-ticket')) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Anda tidak memiliki izin untuk mengeskalasi tiket ini.',
                ], 403);
            }

            // Pastikan hanya user yang sedang menangani tiket yang bisa melakukan eskalasi
            if ($ticket->assigned_to !== $user->id) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Anda tidak dapat mengeskalasi tiket yang tidak Anda tangani.',
                ], 403);
            }

            // Pastikan tiket belum di-escalate sebelumnya
            if ($ticket->is_escalation === true) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Tiket ini sudah dalam status eskalasi.',
                ], 400);
            }

            $ticket->update([
                'is_escalation' => true,
                'escalated_at' => now(),
                'status' => 'In Progress',
                'assigned_to' => null, // kosongkan karena belum ditangani admin
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Tiket berhasil di-escalate ke admin.',
                'data' => $ticket,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengeskalasi tiket.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * 🔹 Handle an escalated ticket (by Admin)
     */
    public function handleEscalatedTicket(Request $request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $user = Auth::user();

            // Hanya admin/koordinator yang boleh menangani tiket eskalasi
            if (! $user->can('handle-escalated-ticket')) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Anda tidak memiliki izin untuk menangani tiket eskalasi.',
                ], 403);
            }

            // Pastikan tiket memang dalam status eskalasi
            if ($ticket->is_escalation !== true) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Tiket ini tidak sedang dalam status eskalasi.',
                ], 400);
            }

            $ticket->update([
                'is_escalation' => false,
                'status' => 'In Progress',
                'assigned_to' => $user->id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Tiket eskalasi kini ditangani oleh admin.',
                'data' => $ticket,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menangani tiket eskalasi.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * 🔹 Close an escalated ticket (by Admin)
     */
    public function closeTicketByAdmin(Request $request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $user = Auth::user();

            // Pastikan hanya admin/koordinator yang bisa menutup tiket eskalasi
            if (! $user->can('handle-escalated-ticket')) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Anda tidak memiliki izin untuk menutup tiket ini.',
                ], 403);
            }

            // Pastikan tiket sedang ditangani oleh admin ini
            if ($ticket->assigned_to !== $user->id) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Anda tidak sedang menangani tiket ini.',
                ], 403);
            }

            // Validasi input solusi
            $validator = Validator::make($request->all(), [
                'solution' => 'required|string|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $start = $ticket->started_at ?: $ticket->created_at;
            $closedAt = now();
            $duration = $start->diffInMinutes($closedAt);

            $ticket->update([
                'solution' => $request->solution,
                'status' => 'Closed',
                'solved_by' => $user->id,
                'solved_at' => $closedAt,
                'duration' => $duration,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Tiket berhasil diselesaikan dan ditutup oleh admin.',
                'data' => $ticket,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menutup tiket eskalasi.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
