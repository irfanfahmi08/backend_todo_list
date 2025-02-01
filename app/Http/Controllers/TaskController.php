<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskCreateRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TaskResource;
use App\Models\Category;
use App\Models\Task;
use DateTime;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function create(int $idCategory, TaskCreateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();
        $date = DateTime::createFromFormat('Y-m-d H:i', $request->due_date);
        $category = Category::where('user_id', $user->id)->where('id', $idCategory)->first();
        if(!$category){
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Category not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        $task = new Task($data);
        $task->status = 'pending';
        $task->category_id = $category->id;
        $task->user_id = $user->id;
        $task->save();

        return response()->json([
            'status' => 'success',
            'data' => new TaskResource($task)
        ], 201);
    }

    public function get(int $idCategory, int $idTask): TaskResource
    {
        $user = Auth::user();
        $category = Category::where('user_id', $user->id)->where('id', $idCategory)->first();
        if(!$category){
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Category not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        $task = Task::where('category_id', $idCategory)->where('id', $idTask)->first();
        if(!$task){
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Task not found'
                    ]
                ]
            ])->setStatusCode(404));
        }
        return new TaskResource($task);
    }

    public function show(int $idCategory): JsonResponse
    {
        $user = Auth::user();
        $category = Category::where('user_id', $user->id)->where('id', $idCategory)->first();
        if(!$category){
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Category not found'
                    ]
                ]
            ])->setStatusCode(404));
        }
        $task = Task::where('category_id', $idCategory)->count();
        return response()->json([
            'status' => 'success',
            'total_tasks' => $task
        ], 200);
    }
}