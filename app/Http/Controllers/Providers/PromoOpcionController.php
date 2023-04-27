<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\FailedJobsCron;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromoOpcionController extends Controller
{
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
        // curl_setopt($ch, CURLOPT_POSTFIELDS, "demo=1"); //Opcional
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
        if (isset($result['error'])) {
            FailedJobsCron::create([
                'name' => 'Promo Opcion',
                'message' => $result['error'],
                'status' => 0,
                'type' =>   1
            ]);
            return;
        }
        // if (!$result['error']) {
        $maxSKU = Product::max('internal_sku');
        $idSku = null;
        if (!$maxSKU) {
            $idSku = 1;
        } else {
            $idSku = (int) explode('-', $maxSKU)[1];
            $idSku++;
        }
        foreach ($result as $product) {
            $data = [
                'internal_sku' => "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT),
                'sku' => $product['item_code'],
                'sku_parent' => $product['parent_code'],
                'name' => $product['name'],
                'price' =>  0,
                'description' => $product['description'],
                'stock' => 0,
                'producto_promocion' => false,
                'producto_nuevo' => false,
                'precio_unico' => true,
                'type_id' => 1,
                'provider_id' => 2,
            ];
            $color = null;
            $slug = mb_strtolower(str_replace(' ', '-', $product['color']));
            $color = Color::where("slug", $slug)->first();
            if (!$color) {
                $color = Color::create([
                    'color' => ucfirst($product['color']), 'slug' => $slug,
                ]);
            }
            $data['color_id'] = $color->id;
            // Verificar si la categoria existe y si no registrarla
            $categoria = null;
            $slug = mb_strtolower(str_replace(' ', '-', $product['family']));
            $categoria = Category::where("slug", $slug)->first();
            if (!$categoria) {
                $categoria = Category::create([
                    'family' => ucfirst($product['family']), 'slug' => $slug,
                ]);
            }
            $attributes = [
                [
                    'attribute' => 'Tamaño',
                    'slug' => 'size',
                    'value' => $product['size'],
                ],
                [
                    'attribute' => 'Material',
                    'slug' => 'material',
                    'value' => $product['material'],
                ],
                [
                    'attribute' => 'Capacidad',
                    'slug' => 'capacity',
                    'value' => $product['capacity'],
                ],
                [
                    'attribute' => 'Impresion',
                    'slug' => 'printing',
                    'value' => $product['printing'],
                ],
                [
                    'attribute' => 'Area de impresion',
                    'slug' => 'printing_area',
                    'value' => $product['printing_area'],
                ],
            ];

            // Verificar si la subcategoria existe y si no registrarla
            $subcategoria = Subcategory::find(1);

            $productExist = Product::where('sku', $product['item_code'])->first();
            if (!$productExist) {
                $newProduct = Product::create($data);
                $newProduct->images()->create([
                    'image_url' => $product['img']
                ]);
                $newProduct->productCategories()->create([
                    'category_id' => $categoria->id,
                    'subcategory_id' => $subcategoria->id,
                ]);
                foreach ($attributes as $attr) {
                    $newProduct->productAttributes()->create($attr);
                }
                $idSku++;
                // dd($newProduct);
            }
        }

        $allProducts = Product::where('provider_id', 3)->where('visible', 1)->get();
        foreach ($allProducts as $key => $value) {
            foreach ($result as $product) {
                if ($value->sku == $product['item_code']) {
                    unset($allProducts[$key]);
                    break;
                }
            }
        }

        foreach ($allProducts as  $value) {
            $value->visible = 0;
            $value->save();
        }

        DB::table('images')->where('image_url', '=', null)->delete();

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
            $price = 0;
            $promocion = false;
            print_r($result);
            echo '<br>';
            if ($result == "" or $result == false) {
                array_push($errors, json_encode(["id" => $product->id, "sku" => $product->sku]));
                break;
            }
            if (!$result['GetPriceResult'] == "") {
                $price = (float)$result['GetPriceResult']['Precios']['FinalPrice'];
                $promocion = $result['GetPriceResult']['Precios']['promocion'] == 'Y' ? true : false;
            } else {
                array_push($errors, json_encode(["id" => $product->id, "sku" => $product->sku]));
            }
            $product->update(['price' => $price, 'producto_promocion' => $promocion]);
        }
        FailedJobsCron::create([
            'name' => 'Promo Opcion',
            'message' => "Productos No encontrados al actualizar el precio: " . implode(", ", $errors),
            'status' => 0,
            'type' =>   1
        ]);
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
        // curl_setopt($ch, CURLOPT_POSTFIELDS, "demo=1"); //Opcional
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

        FailedJobsCron::create([
            'name' => 'Promo Opcion',
            'message' => "Productos No encontrados al actualizar el precio: " . implode(",", $errors),
            'status' => 0,
            'type' =>   1
        ]);

        return $errors;
    }
}
