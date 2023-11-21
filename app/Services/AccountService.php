<?php

namespace App\Services;

use App\Models\Account;
use Ramsey\Uuid\Uuid;

class AccountService
{
    public function store($request)
    {
        return Account::create([
            'id' => Uuid::uuid4()->toString(),
            'user_id' => $request->input('user_id'),
            'currency' => $request->input('currency'),
            'balance' => $request->input('balance'),
        ]);
    }
}