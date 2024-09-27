<?php

namespace App\Http\Controllers;

use App\Models\Exchange_Rate;
use Illuminate\Http\Request;
use Goutte\Client;


class ScrapingController extends Controller
{
    public function getCurrencyGTMtoMX()
    {
        // Crear una instancia del cliente Goutte
        $client = new Client();

        // URL de Google Finance para GTQ a MXN
        $url = 'https://www.google.com/finance/quote/GTQ-MXN?sa=X&ved=2ahUKEwjfg56q1eOIAxU-IUQIHcaSF6wQmY0JegQIGxAw';

        // Hacer la solicitud a la página
        $crawler = $client->request('GET', $url);

        // Extraer el valor del tipo de cambio (asegúrate de que el selector CSS sea correcto)
        $exchangeRate = $crawler->filter('.YMlKec.fxKbKc')->first()->text();

        // Limpia el formato si es necesario (remueve símbolos o espacios)
        $exchangeRate = preg_replace('/[^\d.]/', '', $exchangeRate);

        //Actualiza el valor de la API
        $exchange = Exchange_Rate::find(1);
        $exchange->rate = $exchangeRate;
        $exchange->save();

        // Retorna el tipo de cambio
        return response()->json(['GTQ_to_MXN' => $exchangeRate]);
    }
}
