<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Exceptions\CurrencyExchangeException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class CurrencyExchangeController extends Controller
{
    public function getTargetAmount($currency, $currencyFrom, $amount, $date)
    {
        $apiKey = env("API_LAYER_KEY");

        $response = Http::withHeaders([
            'apikey' => $apiKey
        ])
        ->get("https://api.apilayer.com/exchangerates_data/convert",[
            'to' => $currency,
            'from' => $currencyFrom,
            'amount' => $amount,
            'date' => $date
        ]);

        if ($response->failed()) {
            throw CurrencyExchangeException::ServiceUnavailableException();
        }
        $targetAmount = number_format($response->json('result'),2);

        return floatval($targetAmount);
    }
}
