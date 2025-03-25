<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;


class UserController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            new Middleware(middleware: 'auth:sanctum', except: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return UserResource::collection($users);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'profile_url' => 'required|url|max:255'
        ]);

        try {
            $validatedData['password'] = Hash::make($validatedData['password']);

            $user = User::create($validatedData);

            return response()->json([
                'message' => 'User created successfully!',
                'user' => new UserResource($user)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create user.',
                'error' => $e->getMessage() // Useful for debugging (remove in production)
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            return new UserResource($user);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $user->delete();

            return response()->json(['message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function changeProfilePictureAndFullName(Request $request)
    {
        try {
            $user = User::find($request->user()->id);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $validatedData = $request->validate([
                'profile_url' => 'required|url|max:255',
                'name' => 'required|string|max:255',
            ]);

            $user->update([
                'profile_url' => $validatedData['profile_url'],
                'name' => $validatedData['name'],
            ]);

            return response()->json([
                'message' => 'User changed name and profile URL successfully',
                'user' => new UserResource($user)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update user profile.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
