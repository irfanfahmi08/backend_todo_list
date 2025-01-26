<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdatePhotoRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserPhotoResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        // $imagePath = null;

        // if ($request->hasFile('profile_picture')) {
        //     $image = $request->file('profile_picture');
        //     $timestamp = now()->format('m-d-Y');
        //     $randomString = Str::random(10);
        //     $extension = $image->getClientOriginalExtension();

        //     $filename = "profile_{$timestamp}_{$randomString}.{$extension}";
        //      // Cek path folder uploads

        //     $image->move(public_path('uploads'), $filename);
        //     $imagePath = 'uploads/' . $filename;
        // }else {
        //     dd('File tidak diterima');
        // }
        $user = new User($data);
        $user->password = Hash::make($data['password']);
        // $user->profile_picture = $imagePath;
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

    public function logout(): JsonResponse
    {
        $user = Auth::user();
        $user->token = null;
        $user->save();

        return response()->json([
            'status' => true,
            'data' => "Success logout"
        ])->setStatusCode(200);
    }

    public function update(UserUpdateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        if(isset($data['username'])) {
            $user->username = $data['username'];
        }
        if(isset($data['name'])) {
            $user->name = $data['name'];
        }  
        if(isset($data['email'])) {
            $user->email = $data['email'];
        }

        if (User::where('username', $data['username'])->orWhere('email', $data['email'])->count() == 1){
            throw new HttpResponseException(response([
                "errors" => [
                    "messages" => [
                        "username & email already exist"]
                        ]
                    ],400 ));
        }
        $user->save();
        return response()->json([
            'status' => 'Success',
            'message' => 'Update Successfully',
            'data' => new UserResource($user)
        ],201);
    }

    public function updatePhoto(UserUpdatePhotoRequest $request): JsonResponse
    {
        $user = Auth::user();

        if ($request->hasFile('photo')) {
            if ($user->profile_picture && file_exists(public_path($user->profile_picture))){
                unlink(public_path($user->profile_picture));
            }
            $image = $request->file('photo');
            $timestamp = now()->format('m-d-Y');
            $randomString = Str::random(10);
            $extension = $image->getClientOriginalExtension();

            $filename = "profile_{$timestamp}_{$randomString}.{$extension}";
            $image->move(public_path('uploads'), $filename);

            $user->profile_picture = 'uploads/' . $filename;
            $user->save();

            return response()->json([
                'status' => 'Success',
                'message' => 'Photo Update Success',
                'data' => new UserPhotoResource($user)
            ]);
        }
        return response()->json([
            'error' => 'No photo uploaded'
        ], 400);
    }

    public function getPhotoProfile(): UserPhotoResource
    {
        $user = Auth::user();
        return new UserPhotoResource($user);
    }

    public function deletePhoto(): JsonResponse
    {
        $user = Auth::user();

        if ($user->profile_picture) {
            if(file_exists(public_path($user->profile_picture))) {
                unlink(public_path($user->profile_picture));
            }

            $user->profile_picture = null;
            $user->save();
            return response()->json([
                'status' => 'Success',
                'message' => 'Photo Deleted Successfully',
                'data' => new UserResource($user)
            ]);
        }
        return response()->json([
            'error' => 'No photo to delete'
        ], 400);
    }
}
