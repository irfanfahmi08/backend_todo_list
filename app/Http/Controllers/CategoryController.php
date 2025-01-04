<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryCreateRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CategoryController extends Controller
{
    public function create(CategoryCreateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        if (Category::where('name', $data['name'])->count() == 1) {
            throw new HttpResponseException(response([
                "errors" => [
                    "messages" => [
                        "Category name already exists"]
                        ]
                    ],400));
        }

        $category = new Category($data);
        $category->user_id = $user->id;
        $category->save();

        return (new CategoryResource($category))->response()->setStatusCode(201);
    }

    public function show(Request $request): JsonResponse
    {
        $user = Auth::user();
        $category = Category::where('user_id', $user->id)->get();
        if(!$category){
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'data not found'
                    ]
                ]
            ])->setStatusCode(404));
        }
        
        return (CategoryResource::collection($category))->response()->setStatusCode(200);
    }

    public function get(int $id): CategoryResource
    {
        $user = Auth::user();
        $category = Category::where('id', $id)->where('user_id', $user->id)->first();
        if(!$category){
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'data not found'
                    ]
                ]
            ])->setStatusCode(404));
        }
        
        return new CategoryResource($category);
    }

    public function update(int $id, CategoryUpdateRequest $request): JsonResponse
    {
        $user = Auth::user();

        $category = Category::where('id', $id)->where('user_id', $user->id)->first();
        if(!$category){
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'data not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        $data = $request->validated();
        $category->fill($data);
        $category->save();

        return response()->json([
            'status' => 'Success',
            'message' => 'Update Successfully',
            'data' => new CategoryResource($category)
        ]);
    }

    public function delete(int $id): JsonResponse
    {
        $user = Auth::user();

        $category = Category::where('id', $id)->where('user_id', $user->id)->first();
        if(!$category){
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'data not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        $category->delete();
        return response()->json([
            'status' => 'Success',
            'message' => 'Deleted Successfully'
        ],200);
    }
}