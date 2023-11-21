<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        // Generate and attach a personal access token to the user
        $token = $user->createToken('personal-token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function getAllUsers()
    {
        $users = User::withCount('accounts')->get();

        return response()->json(['users' => $users]);
    }
}