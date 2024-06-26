<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\FailedJobsCron;
use App\Models\Product;
use App\Models\Subcategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InnovationController extends Controller
{
    public function getAllProductsInnova()
    {
        try {
            $responseData = [];
            $user_api = "frjrEhY602674c12ce2dm586";
            $api_key = "OM5rkL-820602674c12ce3b6GNoUjiOvnZF8x";
            $wsdl = "https://ws.innovation.com.mx/index.php?wsdl";
            $client = new \nusoap_client($wsdl, 'wsdl');
            $err = $client->getError();
            if ($err) { //MOSTRAR ERRORES
                echo '<h2>Constructor error</h2>' . $err;
                FailedJobsCron::create([
                    'name' => 'Innovation',
                    'message' => $err,
                    'status' => 0,
                    'type' =>   1
                ]);
                exit();
            }
            $params = array('user_api' => $user_api, 'api_key' => $api_key, 'format' => 'JSON'); //PARAMETROS
            $response = $client->call('Pages', $params); //MÉTODO PARA OBTENER EL NÚMERO DE PÁGINAS ACTIVAS
            $response = json_decode($response, true);
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
            $maxSKU = Product::max('internal_sku');
            $idSku = null;
            if (!$maxSKU) {
                $idSku = 1;
            } else {
                $idSku = (int) explode('-', $maxSKU)[1];
                $idSku++;
            }
            $dataSkus = [];
           
            foreach ($responseData as $product) {

                foreach ($product->colores as $colorWS) {
                    array_push($dataSkus, ["sku" => $colorWS->clave]);
                }

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

                // Verificar si la subcategoria existe y si no registrarla
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
                    'price' =>   $product->lista_precios[0]->mi_precio,
                    'description' => $product->descripcion,
                    'stock' => 0,
                    'producto_promocion' => false,
                    'producto_nuevo' => $product->nuevo == "0" ? false : true,
                    'precio_unico' => false,
                    'type_id' => 1,
                    'provider_id' => 3,
                ];
                $data['image'] = [];
                foreach ($product->images as $image) {
                    array_push($data['image'], ['image_url' => $image->image]);
                }
                $tecnicas = [];
                foreach ($product->tecnicas_impresion as $tecnica) {
                    array_push($tecnicas, $tecnica->nombre);
                }
                $attributes = [
                    [
                        'attribute' => 'Material',
                        'slug' => 'material',
                        'value' => $product->material,
                    ],
                    [
                        'attribute' => 'Area de impresion',
                        'slug' => 'area_impresion',
                        'value' => $product->area_impresion,
                    ],
                    [
                        'attribute' => 'Medidas del producto',
                        'slug' => 'medidas_producto',
                        'value' => $product->medidas_producto,
                    ],
                    [
                        'attribute' => 'Peso del producto',
                        'slug' => 'peso_producto',
                        'value' => $product->peso_producto,
                    ],
                    [
                        'attribute' => 'Cantidad por paquete',
                        'slug' => 'cantidad_por_paquete',
                        'value' => $product->cantidad_por_paquete,
                    ],
                    [
                        'attribute' => 'Medidas del paquete',
                        'slug' => 'medidas_paquete',
                        'value' => $product->medidas_paquete,
                    ],
                    [
                        'attribute' => 'Peso del paquete',
                        'slug' => 'peso_paquete',
                        'value' => $product->peso_paquete,
                    ],
                    [
                        'attribute' => 'Tecnica de Impresion',
                        'slug' => 'tecnicas_impresion',
                        'value' => implode(', ', $tecnicas),
                    ],
                ];

                foreach ($product->colores as $colorWS) {
                    // Verificar si el color existe y si no registrarla
                    $color = null;
                    $slug = mb_strtolower(str_replace(' ', '-', $colorWS->codigo_color));
                    $color = Color::where("slug", $slug)->first();
                    if (!$color) {
                        $color = Color::create([
                            'color' => ucfirst($colorWS->codigo_color), 'slug' => $slug,
                        ]);
                    }
                    $data['internal_sku'] = "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT);
                    $data['sku'] = $colorWS->clave;
                    $imagenColor = $colorWS->image;
                    $data['color_id'] = $color->id;
                    $imagenes = $data['image'];
                    if ($data['image'] != null) {
                        array_unshift($imagenes, ['image_url' => $imagenColor]);
                    }
                    $productExist = Product::where('sku', $colorWS->clave)->where('provider_id', 3)->where('visible',1)->first();
                    if (!$productExist) {
                        $newProduct = Product::create($data);
                        $newProduct->productCategories()->create([
                            'category_id' => $categoria->id,
                            'subcategory_id' => $subcategoria->id,
                        ]);
                        foreach ($imagenes as $imagen) {
                            $newProduct->images()->create($imagen);
                        }
                        foreach ($product->lista_precios as $precio) {
                            $newProduct->precios()->create(
                                [
                                    'price' => $precio->mi_precio,
                                    'escala' => $precio->escala,
                                ]
                            );
                        }

                        foreach ($attributes as $attr) {
                            $newProduct->productAttributes()->create($attr);
                        }

                        $idSku++;
                        // dd($/newProduct);
                    } else {
                        $productExist->precios()->delete();
                        $productExist->price = floatval($product->lista_precios[0]->mi_precio);
                        $productExist->visible = 1;
                        $productExist->save();
                        foreach ($product->lista_precios as $precio) {
                            $productExist->precios()->create(
                                [
                                    'price' => $precio->mi_precio,
                                    'escala_inicial' => $precio->mi_precio,
                                ]
                            );
                        }
                        $productExist->images()->delete();
                        foreach ($imagenes as $imagen) {
                            $productExist->images()->create($imagen);
                        }
                    }
                }
            } 

            DB::table('images')->where('image_url', '=', null)->delete();
            return $responseData;
        } catch (Exception $e) {
            FailedJobsCron::create([
                'name' => 'Innovation',
                'message' => $e->getMessage(),
                'status' => 0,
                'type' =>   1
            ]);
            return [$e->getMessage(), $e];
        }
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
                        // print_r($exist);
                        // echo '<br><br>';
                        array_push($responseData, $exist);
                    }
                }
            }
        } else {
            return $response;
        }

        $productsNotFound = [];
        foreach ($responseData as $product) {
            $productCatalogo = Product::where('sku', $product->clave)->first();
            if ($productCatalogo) {
                $productCatalogo->stock = $product->general_stock;
                $productCatalogo->visible = 1;
                $productCatalogo->save();
            } else {
                array_push($productsNotFound, $product->clave);
            }
        }

        FailedJobsCron::create([
            'name' => 'Innovation',
            'message' => "Productos No encontrados al actualizar el stock: " . implode(",", $productsNotFound),
            'status' => 0,
            'type' =>   1
        ]);
    }

    public function cleanAllProductsInnova() {

        try {
            $responseData = [];
            $user_api = "frjrEhY602674c12ce2dm586";
            $api_key = "OM5rkL-820602674c12ce3b6GNoUjiOvnZF8x";
            $wsdl = "https://ws.innovation.com.mx/index.php?wsdl";
            $client = new \nusoap_client($wsdl, 'wsdl');
            $err = $client->getError();
            if ($err) { //MOSTRAR ERRORES
                echo '<h2>Constructor error</h2>' . $err;
                FailedJobsCron::create([
                    'name' => 'Innovation',
                    'message' => $err,
                    'status' => 0,
                    'type' =>   1
                ]);
                exit();
            }
            $params = array('user_api' => $user_api, 'api_key' => $api_key, 'format' => 'JSON'); //PARAMETROS
            $response = $client->call('Pages', $params); //MÉTODO PARA OBTENER EL NÚMERO DE PÁGINAS ACTIVAS
            $response = json_decode($response, true);
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
            $maxSKU = Product::max('internal_sku');
            $idSku = null;
            if (!$maxSKU) {
                $idSku = 1;
            } else {
                $idSku = (int) explode('-', $maxSKU)[1];
                $idSku++;
            }


            // Obtener todos los productos almacenados en la base de datos del proveedor 3
            $allProducts = Product::where('provider_id', 3)->get();
            
            // Arreglo para almacenar los SKU de los productos recibidos del proveedor
            $dataSkus = [];
            
            // Recopilar los SKU de los productos recibidos del proveedor
            foreach ($responseData as $product) {
                foreach ($product->colores as $colorWS) {
                    $dataSkus[] = $colorWS->clave;
                }
            }
            
            // Identificar productos en la base de datos que no coinciden con los productos recibidos
            $productsNotInData = $allProducts->reject(function ($product) use ($dataSkus) {
                return in_array($product->sku, $dataSkus);
            });
            
            // Mover los productos no coincidentes al proveedor 1983 y establecerlos como no visibles
            foreach ($productsNotInData as $product) {
                $product->provider_id = 1983;
                $product->visible = 1; // Cambiar a 1 para hacerlos visibles
                $product->save();
            }
            
            // Crear un arreglo para almacenar los SKU de los productos y contarlos
            $skuCounts = [];
            

            // Obtener los SKU de los productos repetidos 3
            $repeatedSkus = DB::select("
            SELECT sku
            FROM products
            WHERE provider_id = 3 
            GROUP BY sku
            HAVING COUNT(*) > 1
            ");

            foreach ($repeatedSkus as $repeatedSku) {
            $sku = $repeatedSku->sku;

            // Obtener el primer producto de cada SKU repetido para el proveedor ID 3
            $firstProductId = DB::selectOne("
                SELECT MIN(id) AS first_id
                FROM products
                WHERE sku = ? AND provider_id = 3 AND visible = 1
            ", [$sku])->first_id;

            // Cambiar la visibilidad a 0 para los productos repetidos, excepto el primero
            DB::table('products')
                ->where('sku', $sku)
                ->where('provider_id', 3)
                ->where('visible', 1)
                ->where('id', '<>', $firstProductId)
                ->update(['visible' => 0]);
            }

            DB::commit();

        } catch (Exception $e) {
            FailedJobsCron::create([
                'name' => 'Innovation',
                'message' => $e->getMessage(),
                'status' => 0,
                'type' =>   1
            ]);
            return [$e->getMessage(), $e];
        }
    }
}
