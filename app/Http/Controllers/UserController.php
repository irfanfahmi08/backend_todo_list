<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (User::where('username', $data['username'])->orWhere('email', $data['email'])->count() == 1){
            throw new HttpResponseException(response([
                "errors" => [
                    "messages" => [
                        "username & email already exist"]
                        ]
                    ],400 ));
        }
        $user = new User($data);
        $user->password = Hash::make($data['password']);
        $user->save();

        return response()->json([
            'status' => 'Success',
            'message' => 'Register Successfully',
            'data' => new UserResource($user)
        ],201);
    }

    public function login(UserLoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user_or_email = User::where('username', $data['username_or_email'])->orWhere('email', $data['username_or_email'])->first();
        
        if(!$user_or_email || !Hash::check($data['password'], $user_or_email->password)){
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "username or password wrong!"
                    ]
                ]
            ], 401));
        }

        $user_or_email->token = Str::uuid()->toString();
        $user_or_email->save();

        return response()->json([
            'status' => 'Success',
            'data' => new UserResource($user_or_email)
        ]);;
    }

    public function get(Request $request): UserResource
    {
        $user = Auth::user();
        return new UserResource($user);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = Auth::user();
        $user->token = null;
        $user->save();

        return response()->json([
            'status' => true,
            'data' => "Success logout"
        ])->setStatusCode(200);
    }
}
