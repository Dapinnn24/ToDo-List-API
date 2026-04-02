<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublicTaskController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

// GUEST — tidak perlu login
Route::get('/public/tasks', [PublicTaskController::class, 'index']);

// AUTH — register & login
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/tasks',               [TaskController::class, 'index']);
    Route::post('/tasks',              [TaskController::class, 'store']);
    Route::get('/tasks/{task}',        [TaskController::class, 'show']);
    Route::put('/tasks/{task}',        [TaskController::class, 'update']);
    Route::patch('/tasks/{task}',      [TaskController::class, 'update']);
    Route::delete('/tasks/{task}',     [TaskController::class, 'destroy']);
    Route::patch('/tasks/{task}/done', [TaskController::class, 'markDone']);

});