<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AllowedCurrencies implements ValidationRule
{
    private const ALLOWED_CURRENCIES = [
        "EUR",
        "USD",
        "GBP",
    ];
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(!in_array($value, self::ALLOWED_CURRENCIES)){
            $fail("The $value is not a supported $attribute.");
        }
    }

    public function message()
    {
        return 'Unsupported :atribute.';
    }
}
