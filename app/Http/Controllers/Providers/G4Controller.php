<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Status;
use App\Models\Subcategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;

class G4Controller extends Controller
{
    public function getProductsPL()
    {
        try {
            $wsdl = "https://distr.ws.g4mexico.com/index.php?wsdl";
            $client = new \nusoap_client($wsdl, 'wsdl');

            //arreglo parámetros para la consulta de un solo producto
            $params = array('user' => 'C2677', 'key' => 'pL%43');
            //arreglo parámetros para la consulta de todos los productos
            //$params=array('user'=>'CXXXX','key'=>'Password');
            //llamada al método getProduct
            $response = $client->call('getProduct', $params);
            $products = (new SimpleXMLElement(base64_decode($response)));
        } catch (Exception $e) {
            Status::create([
                'name_provider' => 'G4',
                'status' => 'Problemas al acceder al servidor',
                'update_sumary' => 'No se pudo acceder al servidor de G4',
            ]);

            return ('Error al acceder al servidor de G4');
        }

        //arreglo parámetros para la consulta de un solo producto
        $params = array('user' => 'C2677', 'key' => 'pL%43');
        //arreglo parámetros para la consulta de todos los productos
        //$params=array('user'=>'CXXXX','key'=>'Password');
        //llamada al método getProduct
        $response = $client->call('getProduct', $params);
        $products = (new SimpleXMLElement(base64_decode($response)));
        $productos = [];

        foreach ($products->producto as $producto) {

            $atributos = $producto->attributes();
            $data = [];
            $data['codigo_producto'] = (string) $atributos['codigo_producto'];
            $data['nombre_producto'] = (string) $atributos['nombre_producto'];
            $data['descripcion'] = (string) $atributos['descripcion'];
            $data['linea'] = (string) $atributos['linea'];
            $data['medidas'] = (string) $atributos['medidas'];
            $data['medida_ancho'] = (string) $atributos['medida_ancho'];
            $data['medida_alto '] = (string) $atributos['medida_alto'];
            $data['medida_fondo'] = (string) $atributos['medida_fondo'];
            $data['codigo_color'] = (string) $atributos['codigo_color'];
            $data['nombre_color'] = (string) $atributos['nombre_color'];
            $data['material'] = (string) $atributos['material'];
            $data['novedad'] = (string) $atributos['novedad'];
            $data['promocion'] = (string) $atributos['promocion'];
            $data['impresion'] = (string) $atributos['impresion'];
            $data['area_impresion'] = (string) $atributos['area_impresion'];
            $data['descriptores'] = (string) $atributos['descriptores'];
            $data['piezas_por_caja'] = (string) $atributos['piezas_por_caja'];
            try {
                $data['imagenes'] = (string) $producto->imagenes->principal->attributes()['url'];
            } catch (Exception $e) {
                $data['imagenes'] = 'default.jpg';
            }
            $precios = [];
            for ($i = 0; $i < count($producto->precios->escala); $i++) {
                $escala = $producto->precios->escala[$i]->attributes();
                $precio = [
                    'escala_inicial' => (string) $escala['rango'],
                    'escala_final' => $i < count($producto->precios->escala) - 1
                        ? (string)((int) $producto->precios->escala[$i + 1]['rango'] - 1)
                        : '',
                    'precio' => (string) $escala['precio']
                ];
                array_push($precios,  $precio);

            }
            $data['precios'] = $precios;
            if (count($data['precios']) > 0) {
                array_push($productos, $data);
            }

        }

            $maxSKU = Product::max('internal_sku');
            $idSku = null;
            if (!$maxSKU) {
                $idSku = 1;
            } else {
                $idSku = (int) explode('-', $maxSKU)[1];
                $idSku++;
            }

            $productExist = Product::where('sku', $product['codigo_producto'])->where('provider_id', 7)->first();
            if (!$productExist) {
                $newProduct = Product::create([
                    'internal_sku' => "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT),
                    'sku' => $product['codigo_producto'],
                    'name' => $product['nombre_producto'],
                    'price' => floatval($product['precios'][0]['precio']),
                    'description' => $product['descripcion'],
                    'stock' => 0,
                    'descuento' => 0,
                    'producto_promocion' => (int)$product['promocion'] == 0 ? false : true,
                    'producto_nuevo' => (int)$product['novedad'] == 0 ? false : true,
                    'precio_unico' => false,
                    'provider_id' => 7,
                    'type_id' => 1,
                    'color_id' => $color->id,
                ]);
                $newProduct->images()->create(['image_url' =>  $product['imagenes']]);

                // Registrar los precios
                foreach ($product['precios'] as $precio) {
                    $newProduct->precios()->create([
                        'escala_inicial' => $precio['escala_inicial'],
                        'escala_final' => $precio['escala_final'],
                        'price' => floatval($precio['precio']), // Convertir a float antes de asignar
                    ]);
                }

                $productExist = Product::where('sku', $product['codigo_producto'])->where('provider_id', 7)->first();
                if (!$productExist) {
                    $newProduct = Product::create([
                        'internal_sku' => "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT),
                        'sku' => $product['codigo_producto'],
                        'name' => $product['nombre_producto'],
                        'price' => floatval($product['precio']),
                        'description' => $product['descripcion'],
                        'stock' => 0,
                        'descuento' => 0,
                        'producto_promocion' => (int)$product['promocion'] == 0 ? false : true,
                        'producto_nuevo' => (int)$product['novedad'] == 0 ? false : true,
                        'precio_unico' => true,
                        'provider_id' => 7,
                        'type_id' => 1,
                        'color_id' => $color->id,
                    ]);
                    $newProduct->images()->create(['image_url' =>  $product['imagen']]);

                    /*
                Registrar en la tabla product_category el producto, categoria y sub categoria
                */
                    $newProduct->productCategories()->create([
                        'category_id' => $categoria->id,
                        'subcategory_id' => $subcategoria->id,
                    ]);

                $attributes = [
                    [
                        'attribute' => 'Medidas',
                        'slug' => 'medidas',
                        'value' => $product['medidas'],
                    ],
                    [
                        'attribute' => 'Ancho',
                        'slug' => 'medida_ancho',
                        'value' => $product['medida_ancho'],
                    ],
                    [
                        'attribute' => 'Alto',
                        'slug' => 'medida_alto ',
                        'value' => $product['medida_alto '],
                    ],
                    [
                        'attribute' => 'Fondo',
                        'slug' => 'medida_fondo',
                        'value' => $product['medida_fondo'],
                    ],
                    [
                        'attribute' => 'Material',
                        'slug' => 'material',
                        'value' => $product['material'],
                    ],
                    [
                        'attribute' => 'Impresion',
                        'slug' => 'impresion',
                        'value' => $product['impresion'],
                    ],
                    [
                        'attribute' => 'Area de Impresion',
                        'slug' => 'area_impresion',
                        'value' => $product['area_impresion'],
                    ],
                    [
                        'attribute' => 'Piezas por caja',
                        'slug' => 'piezas_por_caja',
                        'value' => $product['piezas_por_caja'],
                    ]
                ];
                foreach ($attributes as $attr) {
                    $newProduct->productAttributes()->create($attr);
                }
                $idSku++;
            } else {
                $productExist->precios()->delete();
                $productExist->update([
                    "price" => floatval($product['precios'][0]['precio'])
                ]);
                // Registrar los precios
                foreach ($product['precios'] as $precio) {
                    $productExist->precios()->create([
                        'escala_inicial' => $precio['escala_inicial'],
                        'escala_final' => $precio['escala_final'],
                        'price' => floatval($precio['precio']), // Convertir a float antes de asignar
                    ]);
                }
            }
        }

        $allProducts = Product::where('provider_id', 7)->get();
        foreach ($productos as $product) {
            foreach ($allProducts as $key => $value) {
                if ($value->sku == $product['codigo_producto']) {
                    break;
                }
            }

        foreach ($allProducts as  $value) {
            $value->visible = 1;
            $value->save();
        }

        $allProducts = Product::where('provider_id', 7)->where('visible', 1)->get();
        foreach ($allProducts as $key => $value) {
            foreach ($productos as $product) {
                foreach ($allProducts as $key => $value) {
                    if ($value->sku == $product['codigo_producto']) {
                        break;
                    }
                }
                unset($allProducts[$key]);
            }

            foreach ($allProducts as  $value) {
                $value->visible = 0;
                $value->save();
            }

            $allProducts = Product::where('provider_id', 7)->where('visible', 1)->get();
            foreach ($allProducts as $key => $value) {
                foreach ($productos as $product) {
                    if ($value->sku == $product['codigo_producto']) {
                        unset($allProducts[$key]);
                        break;
                    }
                }
            }

            foreach ($allProducts as  $value) {
                $value->visible = 0;
                $value->save();
            }

            /* Status::create([
                'name_provider' => 'G4',
                'status' => 'Actualizacion Completa al servidor',
                'update_sumary' => 'Actualizacion Completa de los productos de G4',
            ]);
 */
            DB::table('images')->where('image_url', '=', null)->delete();

            return $productos;
        } catch (Exception) {

            Status::create([
                'name_provider' => 'G4',
                'status' => 'Actualización incompleta al servidor',
                'update_sumary' => 'Actualización incompleta del productos del servidor de G4',
            ]);

            return ('Actualizacion incompleta de productos del servidor de G4');
        }
    }


    public function getAllStockPL()
    {
        try {
            $wsdl = "https://distr.ws.g4mexico.com/index.php?wsdl";
            $client = new \nusoap_client($wsdl, 'wsdl');

            //arreglo parámetros para la consulta de un solo producto
            $params = array('user' => 'C2677', 'key' => 'pL%43');
            //llamada al método getProduct
            $response = $client->call('getProductStock', $params);
            $products = (new SimpleXMLElement(base64_decode($response)));
        } catch (Exception $e) {
            Status::create([
                'name_provider' => 'G4',
                'status' => 'Problemas al acceder al servidor',
                'update_sumary' => 'No se pudo acceder al servidor de G4',
            ]);
        }

        //arreglo parámetros para la consulta de un solo producto
        $params = array('user' => 'C2677', 'key' => 'pL%43');
        //llamada al método getProduct
        $response = $client->call('getProductStock', $params);
        $products = (new SimpleXMLElement(base64_decode($response)));

        $productos = [];
        foreach ($products->producto as $producto) {
            $atributos = $producto->attributes();
            $data = [];
            $data['codigo_producto'] = (string) $atributos['codigo_producto'];
            $data['existencias'] = (string) $atributos['existencias'];
            array_push($productos, $data);
        }

        $errors = [];
        foreach ($productos as $product) {
            $productCatalogo = Product::where('provider_id', 7)->where('sku',  $product['codigo_producto'])->first();
            if ($productCatalogo) {
                $productCatalogo->update(['stock' => (int)$product['existencias']]);
            } else {
                array_push($errors, $product['existencias']);
            }


        // Eliminar los productos que no estan en el catalogo
        $allProducts = Product::where('provider_id', 7)->get();
        foreach ($productos as $product) {
            foreach ($allProducts as $key => $value) {
                if ($value->sku == $product['codigo_producto']) {
                    break;
                }
                unset($allProducts[$key]);
            }
        }
        foreach ($allProducts as  $value) {
            $value->visible = 0;
            $value->save();
        }

        $allProducts = Product::where('provider_id', 7)->where('visible', 1)->get();
        foreach ($allProducts as $key => $value) {
            foreach ($productos as $product) {
                $productCatalogo = Product::where('sku',  $product['codigo_producto'])->first();
                if ($productCatalogo) {
                    $productCatalogo->update(['stock' => (int)$product['existencias']]);
                } else {
                    array_push($errors, $product['existencias']);
                }
            }


            // Eliminar los productos que no estan en el catalogo
            $allProducts = Product::where('provider_id', 7)->get();
            foreach ($productos as $product) {
                foreach ($allProducts as $key => $value) {
                    if ($value->sku == $product['codigo_producto']) {
                        break;
                    }
                }
                unset($allProducts[$key]);
            }
            foreach ($allProducts as  $value) {
                $value->visible = 0;
                $value->save();
            }

            $allProducts = Product::where('provider_id', 7)->where('visible', 1)->get();
            foreach ($allProducts as $key => $value) {
                foreach ($productos as $product) {
                    if ($value->sku == $product['codigo_producto']) {
                        unset($allProducts[$key]);
                        break;
                    }
                }
            }

            foreach ($allProducts as  $value) {
                $value->visible = 0;
                $value->save();
            }

            /*  Status::create([
                'name_provider' => 'G4',
                'status' => 'Actualizacion Completa al servidor',
                'update_sumary' => 'Actualizacion Completa de los productos de G4',
            ]); */
        } catch (Exception $e) {
            Status::create([
                'name_provider' => 'G4',
                'status' => 'Actualización incompleta al servidor',
                'update_sumary' => 'Actualización incompleta de stock del servidor de G4',
            ]);

            return ('Actualización incompleta de stock del servidor de G4');
        }
    }



    public function getProductsBH()
    {

        try {
            $wsdl = "https://distr.ws.g4mexico.com/index.php?wsdl";
            $client = new \nusoap_client($wsdl, 'wsdl');

            $err = $client->getError();

            //arreglo parámetros para la consulta de un solo producto
            $params = array('user' => 'C3030', 'key' => '19bh@Fya');
            //arreglo parámetros para la consulta de todos los productos
            //$params=array('user'=>'CXXXX','key'=>'Password');
            //llamada al método getProduct
            $response = $client->call('getProduct', $params);
            $products = (new SimpleXMLElement(base64_decode($response)));
        } catch (Exception $e) {
            Status::create([
                'name_provider' => 'G4',
                'status' => 'Problemas al acceder al servidor',
                'update_sumary' => 'No se pudo acceder al servidor de G4',
            ]);

            return ('Error al acceder al servidor de G4');
        }

        try {
            $productos = [];
            foreach ($products as $producto) {
                $atributos = $producto->attributes();
                $data = [];
                $data['codigo_producto'] = (string) $atributos['codigo_producto'];
                $data['nombre_producto'] = (string) $atributos['nombre_producto'];
                $data['descripcion'] = (string) $atributos['descripcion'];
                $data['linea'] = (string) $atributos['linea'];
                $data['medidas'] = (string) $atributos['medidas'];
                $data['medida_ancho'] = (string) $atributos['medida_ancho'];
                $data['medida_alto '] = (string) $atributos['medida_alto'];
                $data['medida_fondo'] = (string) $atributos['medida_fondo'];
                $data['codigo_color'] = (string) $atributos['codigo_color'];
                $data['nombre_color'] = (string) $atributos['nombre_color'];
                $data['material'] = (string) $atributos['material'];
                $data['novedad'] = (string) $atributos['novedad'];
                $data['promocion'] = (string) $atributos['promocion'];
                $data['impresion'] = (string) $atributos['impresion'];
                $data['area_impresion'] = (string) $atributos['area_impresion'];
                $data['descriptores'] = (string) $atributos['descriptores'];
                $data['piezas_por_caja'] = (string) $atributos['piezas_por_caja'];
                try {
                    $data['imagen'] = (string) $producto->imagenes->principal->attributes()['url'];
                } catch (Exception $e) {
                    $data['imagen'] = 'default.jpg';
                }
                $precios = [];
                for ($i = 0; $i < count($producto->precios->escala); $i++) {
                    $escala = $producto->precios->escala[$i]->attributes();
                    $precio = [
                        'escala_inicial' => (string) $escala['rango'],
                        'escala_final' => $i < count($producto->precios->escala) - 1
                            ? (string)((int) $producto->precios->escala[$i + 1]['rango'] - 1)
                            : '',
                        'precio' => (string) $escala['precio']
                    ];
                    array_push($precios,  $precio);
                }
                $data['precios'] = $precios;
                if (count($data['precios']) > 0) {
                    array_push($productos, $data);
                }
            }

            $maxSKU = Product::max('internal_sku');
            $idSku = null;
            if (!$maxSKU) {
                $idSku = 1;
            } else {
                $idSku = (int) explode('-', $maxSKU)[1];
                $idSku++;
            }

            foreach ($productos as $product) {
                // Verificar si el color existe y si no registrarla
                $color = null;
                $slug = mb_strtolower(str_replace(' ', '-', $product['nombre_color']));
                $color = Color::where("slug", $slug)->first();
                if (!$color) {
                    $color = Color::create([
                        'color' => ucfirst($product['nombre_color']), 'slug' => $slug,
                    ]);
                }
                // Verificar si la categoria existe y si no registrarla
                $categoria = null;
                $slug = mb_strtolower(str_replace(' ', '-', $product['linea']));
                $categoria = Category::where("slug", $slug)->first();
                if (!$categoria) {
                    $categoria = Category::create([
                        'family' => ucfirst($product['linea']), 'slug' => $slug,
                    ]);
                }
                // Verificar si la subcategoria existe y si no registrarla
                $subcategoria = Subcategory::find(1);

                $productExist = Product::where('sku', $product['codigo_producto'])->where('provider_id', 8)->first();
                if (!$productExist) {
                    $newProduct = Product::create([
                        'internal_sku' => "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT),
                        'sku' => $product['codigo_producto'],
                        'name' => $product['nombre_producto'],
                        'price' => floatval($product['precios'][0]['precio']),
                        'description' => $product['descripcion'],
                        'stock' => 0,
                        'descuento' => 0,
                        'producto_promocion' => (int)$product['promocion'] == 0 ? false : true,
                        'producto_nuevo' => (int)$product['novedad'] == 0 ? false : true,
                        'precio_unico' => false,
                        'provider_id' => 8,
                        'type_id' => 1,
                        'color_id' => $color->id,
                    ]);
                    $newProduct->images()->create(['image_url' =>  $product['imagen']]);

                    // Registrar los precios
                    foreach ($product['precios'] as $precio) {
                        $newProduct->precios()->create([
                            'escala_inicial' => $precio['escala_inicial'],
                            'escala_final' => $precio['escala_final'],
                            'price' => $precio['precio'],
                        ]);
                    }

                    /*
                Registrar en la tabla product_category el producto, categoria y sub categoria
                */
                    $newProduct->productCategories()->create([
                        'category_id' => $categoria->id,
                        'subcategory_id' => $subcategoria->id,
                    ]);


                    $attributes = [
                        [
                            'attribute' => 'Medidas',
                            'slug' => 'medidas',
                            'value' => $product['medidas'],
                        ],
                        [
                            'attribute' => 'Ancho',
                            'slug' => 'medida_ancho',
                            'value' => $product['medida_ancho'],
                        ],
                        [
                            'attribute' => 'Alto',
                            'slug' => 'medida_alto ',
                            'value' => $product['medida_alto '],
                        ],
                        [
                            'attribute' => 'Fondo',
                            'slug' => 'medida_fondo',
                            'value' => $product['medida_fondo'],
                        ],
                        [
                            'attribute' => 'Material',
                            'slug' => 'material',
                            'value' => $product['material'],
                        ],
                        [
                            'attribute' => 'Impresion',
                            'slug' => 'impresion',
                            'value' => $product['impresion'],
                        ],
                        [
                            'attribute' => 'Area de Impresion',
                            'slug' => 'area_impresion',
                            'value' => $product['area_impresion'],
                        ],
                        [
                            'attribute' => 'Piezas por caja',
                            'slug' => 'piezas_por_caja',
                            'value' => $product['piezas_por_caja'],
                        ]
                    ];
                    foreach ($attributes as $attr) {
                        $newProduct->productAttributes()->create($attr);
                    }
                    $idSku++;
                } else {
                    $productExist->precios()->delete();
                    $productExist->update([
                        "price" => floatval($product['precios'][0]['precio'])
                    ]);
                    foreach ($product['precios'] as $precio) {
                        $productExist->precios()->create([
                            'escala_inicial' => $precio['escala_inicial'],
                            'escala_final' => $precio['escala_final'],
                            'price' => $precio['precio'],
                        ]);
                    }
                }
            }

            $allProducts = Product::where('provider_id', 8)->get();
            foreach ($productos as $product) {
                foreach ($allProducts as $key => $value) {
                    if ($value->sku == $product['codigo_producto']) {
                        break;
                    }
                }
                unset($allProducts[$key]);
            }

            foreach ($allProducts as  $value) {
                $value->visible = 0;
                $value->save();
            }

            $allProducts = Product::where('provider_id', 7)->where('visible', 1)->get();
            foreach ($allProducts as $key => $value) {
                foreach ($productos as $product) {
                    if ($value->sku == $product['codigo_producto']) {
                        unset($allProducts[$key]);
                        break;
                    }
                }
            }

            foreach ($allProducts as  $value) {
                $value->visible = 0;
                $value->save();
            }

            // Status::create([
            //     'name_provider' => 'G4',
            //     'status' => 'Actualizacion Completa al servidor',
            //     'update_sumary' => 'Actualizacion Completa de los productos de G4',
            // ]);

            DB::table('images')->where('image_url', '=', null)->delete();
        } catch (Exception $e) {
            Status::create([
                'name_provider' => 'G4',
                'status' => 'Actualización incompleta al servidor',
                'update_sumary' => 'Actualización incompleta de produtos del servidor de G4',
            ]);

            return ('Actualización incompleta del productos de G4');
        }
    }
}

// TODO: Estoy revisando la estructura de los datos de G4 BH
