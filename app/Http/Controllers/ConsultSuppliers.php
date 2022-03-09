<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

include('nusoap/nusoap.php');

class ConsultSuppliers extends Controller
{
    public function getAllProductsInnova()
    {
        // try {
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
        // return $responseData;
        foreach ($responseData as $product) {
            $data = [
                'sku_parent' => $product->codigo,
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
                if ($data['image'] == null) {
                    $data['image'] = $product->images[0]->image;
                }
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
        // } catch (Exception $e) {
        //     return   $e->getMessage();
        // }
    }

    public function getStockInnova()
    {
        $user_api = "frjrEhY602674c12ce2dm586";
        $api_key = "OM5rkL-820602674c12ce3b6GNoUjiOvnZF8x";
        $wsdl = "https://ws.innovation.com.mx/index.php?wsdl";
        $client = new \nusoap_client($wsdl, 'wsdl');
        $err = $client->getError();
        if ($err) { //MOSTRAR ERRORES
            echo '<h2>Constructor error</h2>' . $err;
            exit();
        }

        $products = Product::where('provider_id', 3)->get();
        $productsConsulted = [];
        foreach ($products as $product) {
            if (!in_array($product->sku_parent, $productsConsulted)) {
                array_push($productsConsulted, $product->sku_parent);
                $params = array('user_api' => $user_api, 'api_key' => $api_key, 'format' => 'JSON', 'code_product' => $product->sku_parent); //PARAMETROS
                $response = $client->call('Stock', $params); //MÉTODO STOCK
                $response = json_decode($response);
                foreach ($response->data->existencias as $stockProduct) {
                    $updateProduct = Product::where('sku', $stockProduct->clave);
                    $updateProduct->update(['stock' => $stockProduct->general_stock]);
                }
            }
        }
    }

    public function getAllProductsPromoOption()
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
        // if (!$result['error']) {
        foreach ($result as $product) {
            $data = [
                'sku' => $product['item_code'],
                'sku_parent' => $product['parent_code'],
                'name' => $product['name'],
                'price' =>  0,
                'description' => $product['description'],
                'stock' => 0,
                'type' => 'Normal',
                'offer' => false,
                'discount' => 0,
                'provider_id' => 2,
                'image' => $product['img'],
                'color' => $product['color'],
            ];
            $productExist = Product::where('sku', $product['item_code'])->first();
            if (!$productExist) {
                $newProduct = Product::create($data);
            } else {
                $productExist->update([
                    'price' => $data['price'],
                    'stock' => 0,
                ]);
            }
        }
        // } else {
        //     return $result;
        // }
        return $result;
    }

    public function getPricePromoOpcion()
    {
        $client = new \nusoap_client('http://desktop.promoopcion.com:8095/wsFullFilmentMXP/FullFilmentMXP.asmx?wsdl', 'wsdl');
        $err = $client->getError();
        if ($err) {
            echo 'Error en Constructor' . $err;
        }
        $CardCode = "DFE4516";
        $pass = 'DIS00048';

        $products = Product::where('provider_id', 2)->get();
        foreach ($products as $product) {
            $param = array('CardCode' => $CardCode, 'pass' => $pass, 'ItemCode' => $product->sku);
            $result = $client->call('GetPrice', $param);
            $price = null;
            if (!$result['GetPriceResult'] == "") {
                $price = (float)$result['GetPriceResult']['Precios']['FinalPrice'];
            }
            $product->update(['price' => $price]);
        }
    }

    public function getStockPromoOpcion()
    {
        $client = new \nusoap_client('http://desktop.promoopcion.com:8095/wsFullFilmentMXP/FullFilmentMXP.asmx?wsdl', 'wsdl');
        $err = $client->getError();
        if ($err) {
            echo 'Error en Constructor' . $err;
        }
        $CardCode = "DFE4516";

        $products = Product::where('provider_id', 2)->get();

        foreach ($products as $product) {
            $param = array('codigo' => $product->sku, 'distribuidor' => $CardCode);
            $result = $client->call('existencias', $param);
            $stock = null;
            if (!$result['existenciasResult'] == "") {
                $stock = (int)$result['existenciasResult']['Existencia']['Stok'];
            }
            $product->update(['stock' => $stock]);
        }
    }

    public function getAllProductsForPromotional()
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
        // $this->downloadImageFP($products[0][''], $products[0]['nombre_articulo']);
        // return $products;
        // $jsonArrayResponse = json_decode($phoneList);
        foreach ($products as $product) {
            $productExist = Product::where('sku', $product['id_articulo'])->where('color', $product['color'])->first();
            // TODO: Verificar si la categoria existe y si no registrarla
            // Variable Categoria

            // TODO: Verificar si la subcategoria existe y si no registrarla
            // Variable subCategoria

            $precio = ($product['precio'] - ($product['precio'] * 0.25));
            $offer = $product['producto_promocion'] == "SI" ? true : false;
            if ($offer) {
                $precio = ($product['precio'] - ($product['precio'] * ($product['desc_promo'] / 100)));
            }
            if (!$productExist) {
                $image = '';
                foreach ($product['imagenes'] as $imagen) {
                    if ($imagen['tipo_imagen'] == 'imagen_color') {
                        $image =  $imagen['url_imagen'];
                    }
                }
                $newProduct = Product::create([
                    'sku' => $product['id_articulo'],
                    'name' => $product['nombre_articulo'],
                    'price' =>  $precio,
                    'description' => $product['descripcion'],
                    'stock' => $product['inventario'],
                    'type' => 'Normal',
                    'color' => $product['color'],
                    'image' => $image,
                    'offer' => $offer,
                    'discount' => $product['desc_promo'],
                    'provider_id' => 1,
                ]);
                /*
                TODO: Registrar en la tabla product_category el producto, categoria y sub categoria


                */
            } else {
                $productExist->update([
                    'price' => $product['precio'],
                    'stock' => $product['inventario'],
                ]);
            }
        }
    }
}
