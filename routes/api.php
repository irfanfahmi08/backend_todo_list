<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/users', [UserController::class, 'register']);
Route::post('/users/login', [UserController::class, 'login']);

Route::middleware(ApiAuthMiddleware::class)->group(function () {
    Route::get('/users/current', [UserController::class, 'get']);
    Route::delete('/users/logout', [UserController::class, 'logout']);

    Route::post('/categories', [CategoryController::class, 'create']);
    Route::get('/categories', [CategoryController::class, 'show']);
    Route::get('/categories/{id}', [CategoryController::class, 'get'])->where('id', '[0-9]+');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/categories/{id}', [CategoryController::class, 'delete'])->where('id', '[0-9]+');
});