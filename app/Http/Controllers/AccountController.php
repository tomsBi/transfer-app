<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Requests\StoreAccountRequest;
use App\Services\AccountService;
use Illuminate\Support\Facades\Auth;

class AccountController extends BaseController
{

    private $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUserAccounts()
    {
        $user = Auth::user();

        User::getUser($user->id);

        $userAccounts = Account::where('user_id', $user->id)->get();

        return response()->json(['user_accounts' => $userAccounts]);
    }

    public function createAccount(StoreAccountRequest $request)
    {
        $user = Auth::user();

        validator($request->all());

        $account = $this->accountService->store($request, $user->id);

        return response()->json(['message' => 'Account created successfully', 'account' => $account]);
    }
}
