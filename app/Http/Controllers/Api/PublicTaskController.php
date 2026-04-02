<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;

class PublicTaskController extends Controller
{
    public function index()
    {
        $tasks = Task::where('is_public', true)->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar task publik berhasil diambil.',
            'data'    => TaskResource::collection($tasks),
        ]);
    }
}