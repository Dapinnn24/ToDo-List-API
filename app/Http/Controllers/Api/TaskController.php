<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // GET /api/tasks
    public function index(Request $request)
    {
        $query = $request->user()->tasks();

        if ($request->has('status')) {
            $request->validate(['status' => 'in:pending,done']);
            $query->where('status', $request->status);
        }

        $tasks = $query->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar task berhasil diambil.',
            'data'    => TaskResource::collection($tasks),
        ]);
    }

    // POST /api/tasks
    public function store(StoreTaskRequest $request)
    {
        $task = $request->user()->tasks()->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Task berhasil dibuat.',
            'data'    => new TaskResource($task),
        ], 201);
    }

    // GET /api/tasks/{id}
    public function show(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Task tidak ditemukan.',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail task berhasil diambil.',
            'data'    => new TaskResource($task),
        ]);
    }

    // PUT/PATCH /api/tasks/{id}
    public function update(UpdateTaskRequest $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Task tidak ditemukan.',
                'data'    => null,
            ], 404);
        }

        $task->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Task berhasil diupdate.',
            'data'    => new TaskResource($task),
        ]);
    }

    // DELETE /api/tasks/{id}
    public function destroy(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Task tidak ditemukan.',
                'data'    => null,
            ], 404);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task berhasil dihapus.',
            'data'    => null,
        ]);
    }

    // PATCH /api/tasks/{id}/done
    public function markDone(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Task tidak ditemukan.',
                'data'    => null,
            ], 404);
        }

        $task->update(['status' => 'done']);

        return response()->json([
            'success' => true,
            'message' => 'Task berhasil ditandai selesai.',
            'data'    => new TaskResource($task),
        ]);
    }
}