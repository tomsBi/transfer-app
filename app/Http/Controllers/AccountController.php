<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Account;
use Illuminate\Routing\Controller as BaseController;
use Ramsey\Uuid\Uuid;

class AccountController extends BaseController
{

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUserAccounts($userId)
    {
        // Validate that the user exists
        $userExists = User::where('id', $userId)->exists();
        if (!$userExists){
            return response()->json(['error' => 'User not found.'], 404);
        }

        $userAccounts = Account::where('user_id', $userId)->get();

        return response()->json(['user_accounts' => $userAccounts]);
    }

    public function createAccount(Request $request, $userId)
    {
        $request->validate([
            'currency' => 'required|in:USD,EUR,GBP',
            'balance' => 'required|numeric|min:0',
        ]);

        $account = Account::create([
            'id' => Uuid::uuid4()->toString(),
            'user_id' => $userId,
            'currency' => $request->input('currency'),
            'balance' => $request->input('balance'),
        ]);

        return response()->json(['message' => 'Account created successfully', 'account' => $account]);
    }

    public function deleteAccount(Request $request, $userId)
    {
        // Validate that the user exists
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        // Validate that the account ID is present in the request body
        $accountId = $request->input('account_id');
        if (!$accountId) {
            return response()->json(['error' => 'Account ID is required in the request body'], 400);
        }

        // Validate that the account belongs to the user
        $account = Account::where('id', $accountId)->where('user_id', $userId)->first();
        if (!$account) {
            return response()->json(['error' => 'Account not found or does not belong to the user'], 404);
        }

        // Perform the deletion
        $account->delete();

        return response()->json(['message' => 'Account deleted successfully']);
    }
}
