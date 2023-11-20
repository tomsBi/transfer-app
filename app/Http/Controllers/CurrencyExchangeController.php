<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CurrencyExchangeController extends Controller
{
    public function getTargetAmount($currency, $targetCurrency, $amount, $date)
    {
        $apiKey = env("API_LAYER_KEY");

        $response = Http::withHeaders([
            'apikey' => $apiKey
        ])
        ->get("https://api.apilayer.com/exchangerates_data/convert",[
            'to' => $targetCurrency,
            'from' => $currency,
            'amount' => $amount,
            'date' => $date
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Unable to fetch exchange rate.'], 500);
        }
        $targetAmount = number_format($response->json('result'),2);

        return floatval($targetAmount);
    }
}
