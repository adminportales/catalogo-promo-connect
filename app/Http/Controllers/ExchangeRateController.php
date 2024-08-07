<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exchange_Rate;

class ExchangeRateController extends Controller
{

    public function getExchangeRate()
    {
        try {

            $url = 'https://serpapi.com/search?engine=google_finance&q=GTQ-MXN&api_key=929bd824e8c1c33819732a8ae5b880de677838d148433c337bb24990ed41d8d0';
            $response = file_get_contents($url);
            $data = json_decode($response);
            $exchange_rate = new Exchange_Rate();
            $exchange_rate->currency_from = explode(' / ', $data->summary->stock)[0];
            $exchange_rate->currency_to = explode(' / ', $data->summary->stock)[1];
            $exchange_rate->rate = $data->summary->price;

            $exist_exchange_rate = Exchange_Rate::where('currency_from', $exchange_rate->currency_from)
                ->where('currency_to', $exchange_rate->currency_to)
                ->first();
            if ($exist_exchange_rate) {
                $exist_exchange_rate->rate = $exchange_rate->rate;
                $exist_exchange_rate->save();
            } else {
                $exchange_rate->save();
            }

            return $exchange_rate;
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
}
