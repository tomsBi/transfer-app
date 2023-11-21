<?php

namespace App\Services;

use App\Models\Account;
use Ramsey\Uuid\Uuid;

class AccountService
{
    public function store($request, $userId)
    {
        return Account::create([
            'id' => Uuid::uuid4()->toString(),
            'user_id' => $userId,
            'currency' => $request->input('currency'),
            'balance' => $request->input('balance'),
        ]);
    }
}