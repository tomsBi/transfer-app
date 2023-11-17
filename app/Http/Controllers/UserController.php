<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function getAllUsers()
    {
        $users = User::withCount('accounts')->get();

        return response()->json(['users' => $users]);
    }
}