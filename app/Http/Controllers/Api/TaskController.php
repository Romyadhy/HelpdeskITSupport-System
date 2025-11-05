<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * 🔹 Get list of tasks (daily or monthly)
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $frequency = $request->query('frequency', 'daily'); // default: daily

            $query = Task::where('frequency', $frequency)
                ->orderBy('title');

            // Non-admin hanya melihat task aktif
            if (! $user->hasRole('admin')) {
                $query->where('is_active', true);
            }

            $tasks = $query->get();

            // Tambahkan status completed untuk user ini
            $completed = TaskCompletion::where('user_id', $user->id)
                ->when($frequency === 'daily', function ($q) {
                    $q->whereDate('complated_at', today());
                })
                ->when($frequency === 'monthly', function ($q) {
                    $q->whereMonth('complated_at', now()->month)
                        ->whereYear('complated_at', now()->year);
                })
                ->pluck('task_id')
                ->toArray();

            // Tambahkan status 'completed' pada tiap task
            $tasks->transform(function ($task) use ($completed) {
                $task->completed = in_array($task->id, $completed);

                return $task;
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Daftar task berhasil diambil.',
                'data' => $tasks,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * 🔹 Show detail of a task
     */
    public function show($id)
    {
        try {
            $task = Task::with(['completions.user'])->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Detail task berhasil diambil.',
                'data' => $task,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task tidak ditemukan atau terjadi kesalahan.',
                'error' => $th->getMessage(),
            ], 404);
        }
    }

    /**
     * 🔹 Create a new task (Admin only)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'frequency' => 'required|in:daily,monthly',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();

        if (! $user->hasRole('admin')) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Anda tidak memiliki izin untuk membuat task.',
            ], 403);
        }

        $task = Task::create($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Task berhasil dibuat.',
            'data' => $task,
        ], 201);
    }

    /**
     * 🔹 Update task (Admin only)
     */
    public function update(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);
            $user = Auth::user();

            if (! $user->hasRole('admin')) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Anda tidak memiliki izin untuk memperbarui task.',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'frequency' => 'sometimes|in:daily,monthly',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $task->update($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Task berhasil diperbarui.',
                'data' => $task,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * 🔹 Delete task (Admin only)
     */
    public function destroy($id)
    {
        try {
            $task = Task::findOrFail($id);
            $user = Auth::user();

            if (! $user->hasRole('admin')) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Anda tidak memiliki izin untuk menghapus task.',
                ], 403);
            }

            $task->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Task berhasil dihapus.',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * 🔹 Mark a task as completed (for current user)
     */
    public function complete(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);
            $user = Auth::user();

            // Pastikan belum selesai hari/bulan ini
            $alreadyDone = TaskCompletion::where('task_id', $task->id)
                ->where('user_id', $user->id)
                ->when($task->frequency === 'daily', fn ($q) => $q->whereDate('complated_at', today()))
                ->when($task->frequency === 'monthly', fn ($q) => $q
                    ->whereMonth('complated_at', now()->month)
                    ->whereYear('complated_at', now()->year))
                ->exists();

            if ($alreadyDone) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Task ini sudah ditandai selesai.',
                ], 400);
            }

            $completion = TaskCompletion::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'complated_at' => now(),
                'notes' => $request->notes,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Task berhasil ditandai selesai.',
                'data' => $completion,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
