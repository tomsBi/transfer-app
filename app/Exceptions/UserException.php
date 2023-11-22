<?php

namespace App\Exceptions;

class UserException extends CustomException
{
    public static function noUserFoundException($userId): UserException
    {
        return new self(message: 'User ' . $userId . ' not found.', code:404);
    }
}
