<?php

namespace App\Exceptions;

class TransactionException extends CustomException
{
    public static function sameAccountException(): TransactionException
    {
        return new self(message: 'Transaction between the same account is not allowed.', code:403);
    }

    public static function wrongCurrencyException(): TransactionException
    {
        return new self(message: 'Wrong currency.', code:400);
    }

    public static function transactionFailedException(): TransactionException
    {
        return new self(message: 'Transaction failed.', code:500);
    }
}
