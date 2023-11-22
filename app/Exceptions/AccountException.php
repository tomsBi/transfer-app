<?php

namespace App\Exceptions;

class AccountException extends CustomException
{
    public static function insufficientFundsException(): AccountException
    {
        return new self(message: 'Insufficient funds in the creditor account.', code:400);
    }

    public static function noAccountsFoundException($accountId): AccountException
    {
        return new self(message: 'Account ' . $accountId . ' not found.', code:404);
    }
}
