<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Task\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/tasks', TaskController::class);
    Route::put('/mark-complete/{task}', [TaskController::class, 'markComplete']);
});