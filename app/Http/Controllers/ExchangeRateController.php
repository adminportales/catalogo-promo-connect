<?php

namespace App\Http\Controllers;

use App\Models\Exchange_Rate;
use Illuminate\Http\Request;

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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\exchange_rate  $exchange_rate
     * @return \Illuminate\Http\Response
     */
    public function show(exchange_rate $exchange_rate)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\exchange_rate  $exchange_rate
     * @return \Illuminate\Http\Response
     */
    public function edit(exchange_rate $exchange_rate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\exchange_rate  $exchange_rate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, exchange_rate $exchange_rate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\exchange_rate  $exchange_rate
     * @return \Illuminate\Http\Response
     */
    public function destroy(exchange_rate $exchange_rate)
    {
        //
    }
}
