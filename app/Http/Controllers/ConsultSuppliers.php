<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

include('nusoap/nusoap.php');

class ConsultSuppliers extends Controller
{
    public function consultInnovation()
    {
        try {
            $user_api = "frjrEhY602674c12ce2dm586";
            $api_key = "OM5rkL-820602674c12ce3b6GNoUjiOvnZF8x";
            $wsdl = "https://ws.innovation.com.mx/index.php?wsdl";
            $client = new \nusoap_client($wsdl, 'wsdl');
            $err = $client->getError();
            if ($err) { //MOSTRAR ERRORES
                echo '<h2>Constructor error</h2>' . $err;
                exit();
            }
            $params = array('user_api' => $user_api, 'api_key' => $api_key, 'format' => 'JSON'); //PARAMETROS
            $response = $client->call('Pages', $params); //MÉTODO PARA OBTENER EL NÚMERO DE PÁGINAS ACTIVAS
            $response = json_decode($response, true);
            $responseData = [];
            if ($response['response'] === true) {
                for ($i = 1; $i <= $response['pages']; $i++) {
                    $params = array('user_api' => $user_api, 'api_key' => $api_key, 'format' => 'JSON', 'page' => $i); //PARAMETROS
                    $responseProducts = json_decode($client->call('Products', $params));
                    foreach ($responseProducts->data as $product) {
                        array_push($responseData, $product);
                    }
                }
            } else {
                return $response;
            }
            foreach ($responseData as $product) {
                $data = [
                    'name' => $product->nombre,
                    'price' =>   $product->lista_precios[0]->precio,
                    'description' => $product->descripcion,
                    'stock' => 0,
                    'type' => 'Normal',
                    'offer' => false,
                    'discount' => 0,
                    'provider_id' => 3,
                ];
                foreach ($product->colores as $color) {
                    $data['sku'] = $color->clave;
                    $data['image'] = $color->image;
                    $data['color'] = $color->codigo_color;
                    $productExist = Product::where('sku', $color->clave)->first();
                    if (!$productExist) {
                        $newProduct = Product::create($data);
                    } else {
                        $productExist->update([
                            'price' => $data['price'],
                            'stock' => 0,
                        ]);
                    }
                }
            }
            return $responseData;
        } catch (Exception $e) {
            return   $e->getMessage();
        }
    }
    public function consultPromoOption()
    {
        $user = "DFE4516";
        $xapikey = "ad3bdbcfd679bf6fd0b97b4b13809b22";
        $headers = array(
            "user: " . $user,
            "x-api-key: " . $xapikey,
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "demo=2"); //Opcional
        curl_setopt(
            $ch,
            CURLOPT_URL,
            "https://www.contenidopromo.com/wsds/mx/catalogo/"
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        curl_close($ch);
        // Convertir en array
        $result = json_decode($result, true);

        return $result;
    }

    public function consulforPromotional()
    {
        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_URL,
            "https://forpromotional.homelinux.com:9090/WsEstrategia/inventario"
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        curl_close($ch);
        // Convertir en array
        $products = json_decode($result, true);
        return $products;
        // $jsonArrayResponse = json_decode($phoneList);
        foreach ($products as $product) {
            $productExist = Product::where('sku', $product['id_articulo'])->where('color', $product['color'])->first();
            $precio = ($product['precio'] - ($product['precio'] * 0.25));
            $offer = $product['producto_promocion'] == "SI" ? true : false;
            if ($offer) {
                $precio = ($product['precio'] - ($product['precio'] * ($product['desc_promo'] / 100)));
            }
            if (!$productExist) {
                $newProduct = Product::create([
                    'sku' => $product['id_articulo'],
                    'name' => $product['nombre_articulo'],
                    'price' =>  $precio,
                    'description' => $product['descripcion'],
                    'stock' => $product['inventario'],
                    'type' => 'Normal',
                    'color' => $product['color'],
                    'image' => 'Ninguna',
                    'offer' => $offer,
                    'discount' => $product['desc_promo'],
                    'provider_id' => 1,
                ]);
            } else {
                $productExist->update([
                    'price' => $product['precio'],
                    'stock' => $product['inventario'],
                ]);
            }
        }
    }
}
