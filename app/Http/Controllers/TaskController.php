<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{

    public function index(Request $request)
    {
        $request->validate([
            'status' => 'nullable|in:pending,completed,canceled',
            'assignee_id' => 'nullable|exists:users,id',
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        $query = Task::query();

        // filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if (auth()->user()->role === 'user') {
            $query->where('assignee_id', auth()->id());
        } elseif ($request->filled('assignee_id')) {
            $query->where('assignee_id', $request->assignee_id);
        }

        if ($request->filled('from')) {
            $query->whereDate('due_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('due_date', '<=', $request->to);
        }

        $tasks = $query->with(['parent','children','assignee','creator'])->get();

        return TaskResource::collection($tasks);
    }

    public function show(Task $task)
    {
        if (auth()->user()->role === 'user' && $task->assignee_id !== auth()->id()) {
            return response()->json(['message' => 'Access denied'], 403);
        }
        $task->load(['parent', 'children', 'assignee', 'creator']);

        return response()->json(new TaskResource($task), 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'assignee_id' => 'nullable|exists:users,id',
            'due_date'    => 'nullable|date',
            'parent_id'   => 'nullable|exists:tasks,id',
        ]);

        $data['created_by_id']= auth()->id();
        $data['status']='pending';

        $task = Task::create($data);

        $task->load(['parent', 'children', 'assignee', 'creator']);

        return response()->json(new TaskResource($task), 201);
    }


    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'title'        => 'sometimes|required|string|max:255',
            'description'  => 'sometimes|nullable|string',
            'assignee_id'  => 'sometimes|nullable|exists:users,id',
            'due_date'     => 'sometimes|nullable|date',
            'parent_id'    => ['sometimes','nullable', 'exists:tasks,id',
                function ($attribute, $value, $fail) use ($task) {
                    if ($value && $value == $task->id) {
                        $fail('A task cannot depend on itself.');
                    }
                },
            ],
            'status'       => 'sometimes|required|in:pending,completed,canceled',
        ]);

        if (isset($data['status']) && $data['status'] === 'completed') {
            $hasIncompleteChildren = $task->children()
                ->where('status', '!=', 'completed')
                ->exists();

            if ($hasIncompleteChildren) {
                return response()->json([
                    'message' => 'Children must be completed first.'
                ], 422);
            }
        }

        $task->update($data);

        $task->load(['parent', 'children', 'assignee', 'creator']);

        return response()->json(new TaskResource($task), 200);
    }


    public function updateStatus(Request $request, Task $task)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,completed,canceled',
        ]);

        if (auth()->user()->role === 'user' && $task->assignee_id !== auth()->id()) {
            return response()->json([
                'message' => 'You can only update your own tasks.'
            ], 403);
        }

        if ($data['status'] === 'completed') {
            $childrenIncomplete = $task->children()
                ->where('status', '!=', 'completed')
                ->exists();

            if ($childrenIncomplete) {
                return response()->json([
                    'message' => 'Children must be completed first.'
                ], 200);
            }
        }

        $task->update([
            'status' => $data['status']
        ]);

        return response()->json(
            new TaskResource($task->load(['parent', 'children', 'assignee', 'creator'])),
            200
        );
    }


    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully.'
        ], 200);
    }


}
