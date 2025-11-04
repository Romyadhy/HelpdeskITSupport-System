<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TicketApiController extends Controller
{
    /**
     * 🔹 Get all tickets (index)
     */
    public function index()
    {
        try {
            $tickets = Ticket::with(['user', 'category', 'location', 'assignee', 'solver'])
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
}
