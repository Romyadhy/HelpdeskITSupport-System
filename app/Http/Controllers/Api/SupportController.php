<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\Ticket;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupportController extends Controller
{
    public function index()
    {
        return response()->json([
            'mesage' => 'helo testing api aman done keren',
        ], 205);
    }

    public function tickets()
    {
        $tickets = Ticket::all();

        return response()->json([
            'message' => 'success',
            'data' => $tickets,
        ], 202);
    }

    public function submitTask(Request $request)
    {
        try {
            // 1️⃣ Cek role user
            $user = $request->user();
            if (! in_array($user->role, ['support', 'admin', 'manager'])) {
                return response()->json(['message' => 'Unauthorized: role not permitted'], 403);
            }

            // 2️⃣ Validasi input
            $validated = $request->validate([
                'report_date' => 'required|date',
                'content' => 'required|string',
                'ticket_ids' => 'nullable|array',
                'ticket_ids.*' => 'integer|exists:tickets,id',
                'escalate_ids' => 'nullable|array', // ticket yang perlu di-escalate
                'escalate_ids.*' => 'integer|exists:tickets,id',
            ]);

            // 3️⃣ Proses database dengan transaksi
            $report = DB::transaction(function () use ($validated, $user) {

                // buat laporan harian
                $daily = DailyReport::create([
                    'user_id' => $user->id,
                    'report_date' => $validated['report_date'],
                    'content' => $validated['content'],
                ]);

                // hubungkan ke tickets (jika ada)
                if (! empty($validated['ticket_ids'])) {
                    $daily->tickets()->attach($validated['ticket_ids']);
                }

                // proses eskalasi ticket (jika ada)
                if (! empty($validated['escalate_ids'])) {
                    Ticket::whereIn('id', $validated['escalate_ids'])
                        ->update([
                            'is_escalation' => true,
                            'escalated_at' => now(),
                        ]);
                }

                return $daily->load('tickets');
            });

            // 4️⃣ Respons sukses
            return response()->json([
                'message' => 'Daily report submitted successfully.',
                'data' => $report,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Error validasi → tampilkan pesan yang jelas
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            // Error umum → log error agar bisa ditelusuri
            Log::error('Error submitting daily report: ' . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong while submitting the report.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
