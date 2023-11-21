<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Account;
use Illuminate\Routing\Controller as BaseController;
use Ramsey\Uuid\Uuid;
use App\Http\Requests\StoreAccountRequest;
use App\Services\AccountService;

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

    public function getUserAccounts($userId)
    {
        User::getUser($userId);

        $userAccounts = Account::where('user_id', $userId)->get();

        return response()->json(['user_accounts' => $userAccounts]);
    }

    public function createAccount(StoreAccountRequest $request)
    {
        validator($request->all());

        $account = $this->accountService->store($request);

        return response()->json(['message' => 'Account created successfully', 'account' => $account]);
    }
}
