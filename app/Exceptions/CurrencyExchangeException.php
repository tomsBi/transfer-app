<?php

namespace App\Exceptions;

use Exception;

class CurrencyExchangeException extends CustomException
{
    public static function ServiceUnavailableException(): CurrencyExchangeException
    {
        return new self(message: 'Unable to fetch exchange rate.', code:500);
    }
}
