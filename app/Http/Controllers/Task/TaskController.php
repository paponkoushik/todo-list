<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function index(): JsonResponse
    {
        $tasks = Task::latest()->paginate(10);

        return response()->json($tasks);
    }
    
    public function store(TaskRequest $request): JsonResponse
    {
        $task = Task::create($request->validated());
        return response()->json($task, 201);
    }

    public function show(Task $task)
    {
        return response()->json($task, 200);
    }
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());

        return response()->json($task, 200);
    }

    public function markComplete(Task $task): JsonResponse
    {
        $task->update(['is_completed' => true]);

        return response()->json($task, 200);
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully.'], 200);
    }
}
