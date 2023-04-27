<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleXMLElement;

class G4Controller extends Controller
{
    public function getProducts()
    {
        $wsdl = "https://distr.ws.g4mexico.com/index.php?wsdl";
        $client = new \nusoap_client($wsdl, 'wsdl');

        $err = $client->getError();
        if ($err) { //MOSTRAR ERRORES
            echo '<h2>Constructor error</h2>' . $err;
            exit();
        }

        //arreglo parámetros para la consulta de un solo producto
        $params = array('user' => 'C2677', 'key' => 'pL%43');
        //arreglo parámetros para la consulta de todos los productos
        //$params=array('user'=>'CXXXX','key'=>'Password');
        //llamada al método getProduct
        $response = $client->call('getProduct', $params);
        $products = (new SimpleXMLElement(base64_decode($response)));

        foreach ($products as $product) {
            $att = "@attributes";
            dd($product->attributes);
            return;
        }
    }
}
