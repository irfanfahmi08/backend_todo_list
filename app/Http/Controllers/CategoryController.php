<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryCreateRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function create(CategoryCreateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        $category = new Category($data);
        $category->user_id = $user->id;
        $category->save();

        return (new CategoryResource($category))->response()->setStatusCode(201);
    }
}
