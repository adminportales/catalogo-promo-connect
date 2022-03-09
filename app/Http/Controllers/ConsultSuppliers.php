<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
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
        // return $response;
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
            $categoria = null;
            if (count($product->categorias->categorias) > 0) {
                $slug = mb_strtolower(str_replace(' ', '-', $product->categorias->categorias[0]->codigo));
                $categoria = Category::where("slug", $slug)->first();
                if (!$categoria) {
                    $categoria = Category::create([
                        'family' => ucfirst($product->categorias->categorias[0]->nombre), 'slug' => $slug,
                    ]);
                }
            } else {
                $categoria = Category::find(1);
            }

            // TODO: Verificar si la subcategoria existe y si no registrarla
            $subcategoria = null;
            if (count($product->categorias->subcategorias) > 0) {
                $slugSub = mb_strtolower(str_replace(' ', '-', $product->categorias->subcategorias[0]->codigo));
                $subcategoria = $categoria->subcategories()->where("slug", $slugSub)->first();

                if (!$subcategoria) {
                    $subcategoria = $categoria->subcategories()->create([
                        'subfamily' => ucfirst($product->categorias->subcategorias[0]->nombre),
                        'slug' => $slugSub,
                    ]);
                }
            } else {
                $subcategoria = Subcategory::find(1);
            }
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
                    $newProduct->productCategories()->create([
                        'category_id' => $categoria->id,
                        'subcategory_id' => $subcategoria->id,
                    ]);
                    // dd($newProduct);
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
        $params = array('user_api' => $user_api, 'api_key' => $api_key, 'format' => 'JSON'); //PARAMETROS
        $response = $client->call('Pages', $params); //MÉTODO PARA OBTENER EL NÚMERO DE PÁGINAS ACTIVAS
        $response = json_decode($response, true);
        // return $response;
        $responseData = [];
        if ($response['response'] === true) {
            for ($i = 1; $i <= $response['pages']; $i++) {
                $params = array('user_api' => $user_api, 'api_key' => $api_key, 'format' => 'JSON', 'page' => $i); //PARAMETROS
                $responseProducts = json_decode($client->call('Stock', $params));
                foreach ($responseProducts->data as $product) {
                    foreach ($product->existencias as $exist) {
                        array_push($responseData, $exist);
                    }
                }
            }
        } else {
            return $response;
        }
        // return $responseData;

        $productsNotFound = [];
        foreach ($responseData as $product) {
            $productCatalogo = Product::where('sku', $product->clave)->first();
            if ($productCatalogo) {
                $productCatalogo->update(['stock' => $product->general_stock]);
            } else {
                array_push($productsNotFound, $product->clave);
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
        curl_setopt($ch, CURLOPT_POSTFIELDS, "demo=1"); //Opcional
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
        // return $result;
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
            // Verificar si la categoria existe y si no registrarla
            $categoria = null;
            $slug = mb_strtolower(str_replace(' ', '-', $product['family']));
            $categoria = Category::where("slug", $slug)->first();
            if (!$categoria) {
                $categoria = Category::create([
                    'family' => ucfirst($product['family']), 'slug' => $slug,
                ]);
            }

            // Verificar si la subcategoria existe y si no registrarla
            $subcategoria = Subcategory::find(1);

            $productExist = Product::where('sku', $product['item_code'])->first();
            if (!$productExist) {
                $newProduct = Product::create($data);
                $newProduct->productCategories()->create([
                    'category_id' => $categoria->id,
                    'subcategory_id' => $subcategoria->id,
                ]);
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
        $errors = [];
        foreach ($products as $product) {
            $param = array('CardCode' => $CardCode, 'pass' => $pass, 'ItemCode' => $product->sku);
            $result = $client->call('GetPrice', $param);
            $price = null;
            if (!$result['GetPriceResult'] == "") {
                $price = (float)$result['GetPriceResult']['Precios']['FinalPrice'];
            } else {
                array_push($errors, ["id" => $product->id, "sku" => $product->sku]);
            }
            $product->update(['price' => $price]);
        }
        return $errors;
    }

    public function getStockPromoOpcion()
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
        curl_setopt($ch, CURLOPT_POSTFIELDS, "demo=1"); //Opcional
        curl_setopt(
            $ch,
            CURLOPT_URL,
            "https://www.contenidopromo.com/wsds/mx/existencias/"
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        curl_close($ch);
        // Convertir en array
        $result = json_decode($result, true);
        // return $response;
        $errors = [];
        foreach ($result as $sku => $stock) {
            $productCatalogo = Product::where('sku', $sku)->first();
            if ($productCatalogo) {
                $productCatalogo->update(['stock' => $stock]);
            } else {
                array_push($errors, $sku);
            }
        }

        return $errors;
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
        // dd($ch);
        $result = curl_exec($ch);
        curl_close($ch);
        // Convertir en array
        $products = json_decode($result, true);
        // return $products;

        foreach ($products as $product) {
            $productExist = Product::where('sku', $product['id_articulo'])->where('color', $product['color'])->first();
            // TODO: Verificar si la categoria existe y si no registrarla
            $categoria = null;
            $slug = mb_strtolower(str_replace(' ', '-', $product['categoria']));
            $categoria = Category::where("slug", $slug)->first();
            if (!$categoria) {
                $categoria = Category::create([
                    'family' => ucfirst($product['categoria']), 'slug' => $slug,
                ]);
            }

            // TODO: Verificar si la subcategoria existe y si no registrarla
            $subcategoria = null;
            $slugSub = mb_strtolower(str_replace(' ', '-', $product['sub_categoria']));
            $subcategoria = $categoria->subcategories()->where("slug", $slugSub)->first();

            if (!$subcategoria) {
                $subcategoria = $categoria->subcategories()->create([
                    'subfamily' => ucfirst($product['sub_categoria']),
                    'slug' => $slugSub,
                ]);
            }

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
                    'ecommerce' => false,
                    'provider_id' => 1,
                ]);
                /*
                TODO: Registrar en la tabla product_category el producto, categoria y sub categoria
                */
                $newProduct->productCategories()->create([
                    'category_id' => $categoria->id,
                    'subcategory_id' => $subcategoria->id,
                ]);
                // dd($product);
            } else {
                $productExist->update([
                    'price' => $product['precio'],
                    'stock' => $product['inventario'],
                ]);
            }
        }
    }
}
