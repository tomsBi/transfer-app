<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Rules\AllowedCurrencies;
use Illuminate\Support\Facades\Validator;

class AllowedCurrenciesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testValidCurrenciesPassValidation()
    {
        $rule = new AllowedCurrencies();

        foreach (AllowedCurrencies::getAllowedCurrencies() as $currency) {
            $validator = Validator::make([$currency => $currency], [$currency => $rule]);

            $this->assertFalse($validator->fails(), "Validation failed for valid currency: $currency");
        }
    }

    public function testInvalidCurrenciesFailValidation()
    {
        $rule = new AllowedCurrencies();

        $invalidCurrency = 'XYZ';

        $validator = Validator::make([$invalidCurrency => $invalidCurrency], [$invalidCurrency => $rule]);

        $this->assertTrue($validator->fails(), "Validation passed for invalid currency: $invalidCurrency");
        $this->assertEquals('The XYZ is not a supported XYZ.', $validator->errors()->first($invalidCurrency));
    }
}
