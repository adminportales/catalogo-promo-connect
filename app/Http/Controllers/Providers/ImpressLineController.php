<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImpressLineController extends Controller
{
    public function getProductsIL()
    {
        $url = 'https://www.impressline.com.mx/api/stock/';
        $data =  [
            "usr" => "compras1@promolife.com.mx",
            "pwd" => "cve4351",
            "clave" => "BOL 00"
        ];
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        $response = curl_exec($curl);
    }
}
