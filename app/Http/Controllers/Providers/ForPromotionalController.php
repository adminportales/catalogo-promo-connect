<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\FailedJobsCron;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ForPromotionalController extends Controller
{
    public function getAllProductsForPromotional()
    {
        $result = null;
        try {
            $ch = curl_init();
            // Check if initialization had gone wrong*
            if ($ch === false) {
                FailedJobsCron::create([
                    'name' => 'For Promotional',
                    'message' => "'failed to initialize'",
                    'status' => 0,
                    'type' =>   1
                ]);
                throw new Exception('failed to initialize');
            }
            curl_setopt(
                $ch,
                CURLOPT_URL,
                "https://4promotional.net:9090/WsEstrategia/inventario"
            );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $result = curl_exec($ch);

            if (strpos($result, "HTTP Status 404 – Not Found") == true) {
                FailedJobsCron::create([
                    'name' => 'For Promotional',
                    'message' => "HTTP Status 404 – Not Found Metodo No encontrado",
                    'status' => 0,
                    'type' =>   1
                ]);
                return 'Error';
            }

            // Check the return value of curl_exec(), too
            if ($result === false) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }

            $arrContextOptions = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );

            // Convertir en array
            //// inicio
            $products = json_decode($result, true);

            $maxSKU = Product::max('internal_sku');
            $idSku = null;
            if (!$maxSKU) {
                $idSku = 1;
            } else {
                $idSku = (int) explode('-', $maxSKU)[1];
                $idSku++;
            }

            foreach ($products as $product) {
                // Verificar si el color existe y si no registrarla
                $color = null;
                $slug = mb_strtolower(str_replace(' ', '-', $product['color']));
                $color = Color::where("slug", $slug)->first();
                if (!$color) {
                    $color = Color::create([
                        'color' => ucfirst($product['color']), 'slug' => $slug,
                    ]);
                }

                // Verificar si la categoria existe y si no registrarla
                $categoria = null;
                $slug = mb_strtolower(str_replace(' ', '-', $product['categoria']));
                $categoria = Category::where("slug", $slug)->first();
                if (!$categoria) {
                    $categoria = Category::create([
                        'family' => ucfirst($product['categoria']), 'slug' => $slug,
                    ]);
                }

                // Verificar si la subcategoria existe y si no registrarla
                $subcategoria = null;
                $slugSub = mb_strtolower(str_replace(' ', '-', $product['sub_categoria']));
                $subcategoria = $categoria->subcategories()->where("slug", $slugSub)->first();

                if (!$subcategoria) {
                    $subcategoria = $categoria->subcategories()->create([
                        'subfamily' => ucfirst($product['sub_categoria']),
                        'slug' => $slugSub,
                    ]);
                }
                $newdiscount = $product['producto_promocion'] == "NO" ? $product['desc_promo'] : 0;
                $discount = $product['producto_promocion'] == "SI" ? $product['desc_promo'] : 0;
                if ($newdiscount > 25) {
                    $discount = $product['desc_promo'];
                } else {
                    $discount = 0;
                }
                $productExist = Product::where('sku', $product['id_articulo'])->where('color_id', $color->id)->where('provider_id',1)->first();
                
                if (!$productExist) {
                    $newProduct = Product::create([
                        'internal_sku' => "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT),
                        'sku' => $product['id_articulo'],
                        'name' => isset($product['nombre_articulo']) ? $product['nombre_articulo'] : '',
                        'price' =>  $product['precio'],
                        'description' => $product['descripcion'],
                        'stock' => $product['inventario'],
                        'producto_promocion' => $product['producto_promocion'] == "SI" ? true : false,
                        'descuento' => $discount,
                        'producto_nuevo' => $product['producto_nuevo'] == "SI" ? true : false,
                        'precio_unico' => true,
                        'provider_id' => 1,
                        'type_id' => 1,
                        'color_id' => $color->id,
                    ]);
                    foreach (array_reverse($product['imagenes']) as $key => $imagen) {
                        // Descargar Imagenes solo Si no existen
                        $errorGetImage = false;
                        $fileImage = "";
                        try {
                            $fileImage = file_get_contents(str_replace(' ', '%20', $imagen['url_imagen']), false, stream_context_create($arrContextOptions));
                        } catch (Exception $th) {
                            $errorGetImage = true;
                        }
                        $newPath = '';
                        if (!$errorGetImage) {
                            $newPath = '/forpromotional/' . $newProduct->sku . 'type' . $key . $color->slug . ' ' . $product['nombre_articulo'] . '.jpg';
                            Storage::append('public' . $newPath, $fileImage);
                            $newProduct->images()->create([
                                'image_url' => url('/storage' . $newPath)
                            ]);
                        } else {
                            $newProduct->images()->create([
                                'image_url' => 'img/default_product_image.jpg'
                            ]);
                        }
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
                            'attribute' => 'Alto del Producto',
                            'slug' => 'medida_producto_alto',
                            'value' => $product['medida_producto_alto'],
                        ],
                        [
                            'attribute' => 'Ancho del Producto',
                            'slug' => 'medida_producto_ancho',
                            'value' => $product['medida_producto_ancho'],
                        ],
                        [
                            'attribute' => 'Area de Impresion',
                            'slug' => 'area_impresion',
                            'value' => $product['area_impresion'],
                        ],
                        [
                            'attribute' => 'Impresion',
                            'slug' => 'metodos_impresion',
                            'value' => $product['metodos_impresion'],
                        ],
                        [
                            'attribute' => 'Alto de la caja',
                            'slug' => 'alto_caja',
                            'value' => $product['alto_caja'],
                        ],
                        [
                            'attribute' => 'Ancho de la caja',
                            'slug' => 'ancho_caja',
                            'value' => $product['ancho_caja'],
                        ],
                        [
                            'attribute' => 'Largo de la caja',
                            'slug' => 'largo_caja',
                            'value' => $product['largo_caja'],
                        ],
                        [
                            'attribute' => 'Peso de la caja',
                            'slug' => 'peso_caja',
                            'value' => $product['peso_caja'],
                        ]
                    ];
                    foreach ($attributes as $attr) {
                        $newProduct->productAttributes()->create($attr);
                    }
                    $idSku++;
                    // dd($newProduct);
                } else {
                    $productExist->update([
                        'price' => $product['precio'],
                        'stock' => $product['inventario'],
                        'producto_promocion' => $product['producto_promocion'] == "SI" ? true : false,
                        'descuento' => $discount,
                        'visible' => 1,
                    ]);
                    if (count($productExist->images) <= 0) {
                        foreach (array_reverse($product['imagenes']) as $key => $imagen) {
                            $errorGetImage = false;
                            $fileImage = "";
                            try {
                                $fileImage = file_get_contents(str_replace(' ', '%20', $imagen['url_imagen']), false, stream_context_create($arrContextOptions));
                            } catch (Exception $th) {
                                $errorGetImage = true;
                            }
                            $newPath = '';
                            if (!$errorGetImage) {
                                $newPath = '/forpromotional/' . $productExist->sku . 'type' . $key . $color->slug . ' ' . $product['nombre_articulo'] . '.jpg';
                                Storage::append('public' . $newPath, $fileImage);
                                $productExist->images()->create([
                                    'image_url' => url('/storage' . $newPath)
                                ]);
                            } else {
                                $productExist->images()->create([
                                    'image_url' => 'img/default_product_image.jpg'
                                ]);
                            }
                        }
                    }
                }
            }

           
            return $result;
        } catch (Exception $e) {
            FailedJobsCron::create([
                'name' => 'For Promotional',
                'message' => $e->getMessage(),
                'status' => 0,
                'type' =>   1
            ]);
            return $e->getMessage();
        }
    }


    public function cleanAllProductsForPromotional()  {

        $result = null;
        try {
            $ch = curl_init();
            // Check if initialization had gone wrong*
            if ($ch === false) {
                FailedJobsCron::create([
                    'name' => 'For Promotional',
                    'message' => "'failed to initialize'",
                    'status' => 0,
                    'type' =>   1
                ]);
                throw new Exception('failed to initialize');
            }
            curl_setopt(
                $ch,
                CURLOPT_URL,
                "https://4promotional.net:9090/WsEstrategia/inventario"
            );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $result = curl_exec($ch);

            if (strpos($result, "HTTP Status 404 – Not Found") == true) {
                FailedJobsCron::create([
                    'name' => 'For Promotional',
                    'message' => "HTTP Status 404 – Not Found Metodo No encontrado",
                    'status' => 0,
                    'type' =>   1
                ]);
                return 'Error';
            }

            // Check the return value of curl_exec(), too
            if ($result === false) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }

            // Convertir en array
            //// inicio
            $products = json_decode($result, true);

            //Funcion para eliminar los productos que no pertenezcan al proveedor
            $allProducts = Product::where('provider_id', 1)->get();

            // Obtener los sku de los productos de la API
            $apiSkus = array_map(function($product) {
                return $product['id_articulo'];
            }, $products);

            // Obtener los colores de los productos de la API
            $apiColors = array_map(function($product) {
                return strtolower($product['color']);
            }, $products);

            // Iterar sobre los productos de la base de datos
            foreach ($allProducts as $value) {
                // Verificar si el sku y el color del producto de la base de datos están en los datos de la API
                if (!in_array($value->sku, $apiSkus) || 
                    !in_array(strtolower($value->color->color), $apiColors)) {
                    // Si no se encuentra el producto en la API, cambiar provider_id y visible
                    $value->visible = 0;
                    $value->provider_id = 1983;
                    $value->save();
                }
            }

            //Cambia el visible a 0  de los productos repetidos, exceptuando el primero de ellos (el original)
            $repeatedProducts = DB::select("
                SELECT id, sku, color_id
                FROM products
                WHERE provider_id = 1 AND visible = 1 AND sku IN (
                    SELECT sku
                    FROM products
                    WHERE provider_id = 1 AND visible = 1
                    GROUP BY sku
                    HAVING COUNT(*) > 1
                )
            ");
    
            foreach ($repeatedProducts as $product) {
                $productId = $product->id;
                $sku = $product->sku;
                $colorId = $product->color_id;
        
                $firstProductId = DB::selectOne("
                    SELECT MIN(id) AS first_id
                    FROM products
                    WHERE sku = ? AND color_id = ? AND provider_id = 1 AND visible = 1
                ", [$sku, $colorId])->first_id;
        
                DB::table('products')
                    ->where('sku', $sku)
                    ->where('color_id', $colorId)
                    ->where('provider_id', 1)
                    ->where('visible', 1)
                    ->where('id', '<>', $firstProductId)
                    ->update(['visible' => 0]);
            }

            DB::commit();

            DB::table('images')->where('image_url', '=', null)->delete();

            return 'actualizacion de productos finalizada' ;

        } catch (Exception $e) {
            FailedJobsCron::create([
                'name' => 'For Promotional',
                'message' => $e->getMessage(),
                'status' => 0,
                'type' =>   1
            ]);
            return $e->getMessage();
        }

        
        
    }

    // public function apiGetProductsFP()
    // {
    //     $ch = curl_init();
    //     // Check if initialization had gone wrong*
    //     if ($ch === false) {
    //         FailedJobsCron::create([
    //             'name' => 'For Promotional',
    //             'message' => "'failed to initialize'",
    //             'status' => 0,
    //             'type' =>   1
    //         ]);
    //         throw new Exception('failed to initialize');
    //     }
    //     curl_setopt(
    //         $ch,
    //         CURLOPT_URL,
    //         "https://forpromotional.homelinux.com:9090/WsEstrategia/inventario"
    //     );
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    //     $result = curl_exec($ch);

    //     if (strpos($result, "HTTP Status 404 – Not Found") == true) {
    //         FailedJobsCron::create([
    //             'name' => 'For Promotional',
    //             'message' => "HTTP Status 404 – Not Found Metodo No encontrado",
    //             'status' => 0,
    //             'type' =>   1
    //         ]);
    //         return 'Error';
    //     }

    //     // Check the return value of curl_exec(), too
    //     if ($result === false) {
    //         throw new Exception(curl_error($ch), curl_errno($ch));
    //     }

    //     // Convertir en array
    //     $products = json_decode($result, true);
    //     return response()->json($products);
    // }

    // public function getAllProductsForPromotionalToOtherServer()
    // {
    //     $result = null;
    //     try {
    //         $ch = curl_init();
    //         // Check if initialization had gone wrong*
    //         if ($ch === false) {
    //             FailedJobsCron::create([
    //                 'name' => 'For Promotional',
    //                 'message' => "'failed to initialize'",
    //                 'status' => 0,
    //                 'type' =>   1
    //             ]);
    //             throw new Exception('failed to initialize');
    //         }
    //         $url_server = 'https://dev-catalogo.promolife.lat';
    //         $url = "{$url_server}/api/getProductsFP";
    //         curl_setopt(
    //             $ch,
    //             CURLOPT_URL,
    //             $url
    //         );
    //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    //         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    //         $result = curl_exec($ch);

    //         if (strpos($result, "HTTP Status 404 – Not Found") == true) {
    //             FailedJobsCron::create([
    //                 'name' => 'For Promotional',
    //                 'message' => "HTTP Status 404 – Not Found Metodo No encontrado",
    //                 'status' => 0,
    //                 'type' =>   1
    //             ]);
    //             return 'Error';
    //         }

    //         // Check the return value of curl_exec(), too
    //         if ($result === false) {
    //             throw new Exception(curl_error($ch), curl_errno($ch));
    //         }

    //         // Convertir en array
    //         $products = json_decode($result, true);
    //         $maxSKU = Product::max('internal_sku');
    //         $idSku = null;
    //         if (!$maxSKU) {
    //             $idSku = 1;
    //         } else {
    //             $idSku = (int) explode('-', $maxSKU)[1];
    //             $idSku++;
    //         }

    //         foreach ($products as $product) {
    //             // Verificar si el color existe y si no registrarla
    //             $color = null;
    //             $slug = mb_strtolower(str_replace(' ', '-', $product['modelo_color']));
    //             $color = Color::where("slug", $slug)->first();
    //             if (!$color) {
    //                 $color = Color::create([
    //                     'modelo_color' => ucfirst($product['modelo_color']), 'slug' => $slug,
    //                 ]);
    //             }

    //             // Verificar si la categoria existe y si no registrarla
    //             $categoria = null;
    //             $slug = mb_strtolower(str_replace(' ', '-', $product['categoria']));
    //             $categoria = Category::where("slug", $slug)->first();
    //             if (!$categoria) {
    //                 $categoria = Category::create([
    //                     'family' => ucfirst($product['categoria']), 'slug' => $slug,
    //                 ]);
    //             }

    //             // Verificar si la subcategoria existe y si no registrarla
    //             $subcategoria = null;
    //             $slugSub = mb_strtolower(str_replace(' ', '-', $product['sub_categoria']));
    //             $subcategoria = $categoria->subcategories()->where("slug", $slugSub)->first();

    //             if (!$subcategoria) {
    //                 $subcategoria = $categoria->subcategories()->create([
    //                     'subfamily' => ucfirst($product['sub_categoria']),
    //                     'slug' => $slugSub,
    //                 ]);
    //             }

    //             $discount = $product['producto_promocion'] == "SI" ? $product['desc_promo'] : 0;

    //             $productExist = Product::where('sku', $product['id_articulo'])->where('color_id', $color->id)->first();
    //             if (!$productExist) {
    //                 $newProduct = Product::create([
    //                     'internal_sku' => "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT),
    //                     'sku' => $product['id_articulo'],
    //                     'name' => $product['nombre'],
    //                     'price' =>  $product['precio'],
    //                     'description' => $product['descripcion'],
    //                     'stock' => $product['cantidad_piezas'],
    //                     'producto_promocion' => $product['producto_promocion'] == "SI" ? true : false,
    //                     'descuento' => $discount,
    //                     'producto_nuevo' => $product['producto_nuevo'] == "SI" ? true : false,
    //                     'precio_unico' => true,
    //                     'provider_id' => 1,
    //                     'type_id' => 1,
    //                     'color_id' => $color->id,
    //                 ]);
    //                 foreach (array_reverse($product['imagenes']) as $imagen) {
    //                     $newProduct->images()->create([
    //                         'image_url' => $imagen['url_imagen']
    //                     ]);
    //                 }
    //                 /*
    //                 Registrar en la tabla product_category el producto, categoria y sub categoria
    //                 */
    //                 $newProduct->productCategories()->create([
    //                     'category_id' => $categoria->id,
    //                     'subcategory_id' => $subcategoria->id,
    //                 ]);

    //                 $attributes = [
    //                     [
    //                         'attribute' => 'Alto del Producto',
    //                         'slug' => 'medida_producto_alto',
    //                         'value' => $product['medida_producto_alto'],
    //                     ],
    //                     [
    //                         'attribute' => 'Ancho del Producto',
    //                         'slug' => 'medida_producto_ancho',
    //                         'value' => $product['medida_producto_ancho'],
    //                     ],
    //                     [
    //                         'attribute' => 'Area de Impresion',
    //                         'slug' => 'area_impresion',
    //                         'value' => $product['area_impresion'],
    //                     ],
    //                     [
    //                         'attribute' => 'Impresion',
    //                         'slug' => 'metodos_impresion',
    //                         'value' => $product['metodos_impresion'],
    //                     ],
    //                     [
    //                         'attribute' => 'Alto de la caja',
    //                         'slug' => 'alto_caja',
    //                         'value' => $product['alto_caja'],
    //                     ],
    //                     [
    //                         'attribute' => 'Ancho de la caja',
    //                         'slug' => 'ancho_caja',
    //                         'value' => $product['ancho_caja'],
    //                     ],
    //                     [
    //                         'attribute' => 'Largo de la caja',
    //                         'slug' => 'largo_caja',
    //                         'value' => $product['largo_caja'],
    //                     ],
    //                     [
    //                         'attribute' => 'Peso de la caja',
    //                         'slug' => 'peso_caja',
    //                         'value' => $product['peso_caja'],
    //                     ],
    //                     [
    //                         'attribute' => 'Piezas de la caja',
    //                         'slug' => 'piezas_caja',
    //                         'value' => $product['piezas_caja'],
    //                     ],
    //                 ];
    //                 foreach ($attributes as $attr) {
    //                     $newProduct->productAttributes()->create($attr);
    //                 }
    //                 $idSku++;
    //                 // dd($newProduct);
    //             } else {
    //                 $productExist->update([
    //                     'price' => $product['precio'],
    //                     'stock' => $product['cantidad_piezas'],
    //                     'producto_promocion' => $product['producto_promocion'] == "SI" ? true : false,
    //                     'descuento' => $discount,
    //                 ]);

    //             }
    //         }
    //         return $products;
    //     } catch (Exception $e) {
    //         FailedJobsCron::create([
    //             'name' => 'For Promotional',
    //             'message' => $e->getMessage(),
    //             'status' => 0,
    //             'type' =>   1
    //         ]);
    //         return $e->getMessage();
    //     }
    // }
}
