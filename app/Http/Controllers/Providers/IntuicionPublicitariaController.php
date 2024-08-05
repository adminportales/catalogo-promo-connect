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
use Illuminate\Support\Facades\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;



class IntuicionPublicitariaController extends Controller
{


    public function getProductsIP()
    {

        $result = null;
        try {
            $ch = curl_init();
            // Check if initialization had gone wrong*
            if ($ch === false) {
                FailedJobsCron::create([
                    'name' => 'Intuicion Publicitaria',
                    'message' => "'failed to initialize'",
                    'status' => 0,
                    'type' =>   1
                ]);
                throw new Exception('failed to initialize');
            }
            curl_setopt($ch, CURLOPT_URL, "http://138.118.8.25:8090/api/Inventario/Acceso");
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                "Username" => "ITFACTORY",
                "Password" => "123456"
            ]));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $result = curl_exec($ch);

            if (strpos($result, "HTTP Status 404 – Not Found") == true) {
                FailedJobsCron::create([
                    'name' => 'Intuicion Publicitaria',
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
            $products = json_decode($result, true)['Data']['Productos'];
            // return $products;

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
                $slug = mb_strtolower(str_replace(' ', '-', $product['Color']));
                $color = Color::where("slug", $slug)->first();
                if (!$color) {
                    $color = Color::create([
                        'color' => ucfirst($product['Color']), 'slug' => $slug,
                    ]);
                }

                // Verificar si la categoria existe y si no registrarla
                $categoria = null;
                $slug = mb_strtolower(str_replace(' ', '-', $product['SubGrupo']));
                $categoria = Category::where("slug", $slug)->first();
                if (!$categoria) {
                    $categoria = Category::create([
                        'family' => ucfirst($product['SubGrupo']), 'slug' => $slug,
                    ]);
                }

                // Verificar si la subcategoria existe y si no registrarla
                $subcategoria = null;
                $slugSub = mb_strtolower(str_replace(' ', '-', $product['SubGrupo']));
                $subcategoria = $categoria->subcategories()->where("slug", $slugSub)->first();

                if (!$subcategoria) {
                    $subcategoria = $categoria->subcategories()->create([
                        'subfamily' => ucfirst($product['SubGrupo']),
                        'slug' => $slugSub,
                    ]);
                }
                // $newdiscount = $product['producto_promocion'] == "NO" ? $product['desc_promo'] : 0;
                // $discount = $product['producto_promocion'] == "SI" ? $product['desc_promo'] : 0;
                // if ($newdiscount > 25) {
                //     $discount = $product['desc_promo'];
                // } else {
                //     $discount = 0;
                // }
                $productExist = Product::where('sku', $product['Codigo'])->where('color_id', $color->id)->where('provider_id', 1984)->first();

                if (!$productExist) {
                    $newProduct = Product::create([
                        'internal_sku' => "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT),
                        'sku' => $product['Codigo'],
                        'name' => isset($product['Marca']) ? $product['Marca'] : '',
                        'price' => floatval(str_replace(',', '', $product['Precio'])),
                        'description' => $product['Descripcion'],
                        'stock' => $product['Existencia'],
                        'producto_promocion' => false,
                        'descuento' => 0,
                        'producto_nuevo' => false,
                        'precio_unico' => true,
                        'provider_id' => 1984,
                        'type_id' => 1,
                        'color_id' => $color->id,
                    ]);
                    if ($product['Imagenes'] != null) {
                        foreach ($product['Imagenes'] as $key => $imagen) {
                            $errorGetImage = false;
                            $fileImage = "";
                            if ($imagen != null) {
                                try {
                                    $fileImage = file_get_contents(str_replace(' ', '%20', $imagen), false, stream_context_create($arrContextOptions));
                                } catch (Exception $th) {
                                    $errorGetImage = true;
                                }
                            } else {
                                $errorGetImage = true;
                            }
                            $newPath = '';
                            if (!$errorGetImage) {
                                $newPath = '/intuicion/' . $newProduct->sku . 'type' . $key . $color->slug . '.jpg';
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
                    } else {
                        $newProduct->images()->create([
                            'image_url' => 'img/default_product_image.jpg'
                        ]);
                    }

                    // if ($imagenes != null) {
                    //     foreach ($imagenes as $imagen) {
                    //         $newProduct->images()->create([
                    //             'image_url' => $imagen
                    //         ]);
                    //     }
                    // }


                    /*
                        Registrar en la tabla product_category el producto, categoria y sub categoria
                        */
                    $newProduct->productCategories()->create([
                        'category_id' => $categoria->id,
                        'subcategory_id' => $subcategoria->id,
                    ]);

                    $attributes = [
                        ['attribute' => 'Clave SAT del Producto', 'slug' => 'ClaveSAT', 'value' => $product['ClaveSAT']],
                        ['attribute' => 'Unidad SAT del Producto', 'slug' => 'UnidadSAT', 'value' => $product['UnidadSAT']],
                        ['attribute' => 'Nombre de Lista del Precio del Producto', 'slug' => 'NombreListaPrecio', 'value' => $product['NombreListaPrecio']],
                        ['attribute' => 'Unidad de Venta', 'slug' => 'UnidadVenta', 'value' => $product['UnidadVenta']],
                        ['attribute' => 'Cantidad Por Paquete', 'slug' => 'CantidadPorPaquete', 'value' => $product['CantidadPorPaquete']],
                        ['attribute' => 'Ficha Tecnica del Producto', 'slug' => 'FichaTecnica', 'value' => $product['FichaTecnica']],
                    ];
                    foreach ($attributes as $attr) {
                        $newProduct->productAttributes()->create($attr);
                    }
                    $idSku++;
                    // dd($newProduct);
                } else {
                    $productExist->update([
                        'price' => floatval(str_replace(',', '', $product['Precio'])),
                        'stock' => $product['Existencia'],
                        'producto_promocion' => false,
                        'descuento' => 0,
                        'visible' => 1,
                    ]);
                    if (count($productExist->images) <= 0) {
                        if ($product['Imagenes'] != null) {
                            foreach ($product['Imagenes'] as $key => $imagen) {
                                // Descargar Imagenes solo Si no existen
                                $errorGetImage = false;
                                $fileImage = "";
                                if ($imagen != null) {
                                    try {
                                        $fileImage = file_get_contents(str_replace(' ', '%20', $imagen), false, stream_context_create($arrContextOptions));
                                    } catch (Exception $th) {
                                        $errorGetImage = true;
                                    }
                                } else {
                                    $errorGetImage = true;
                                }
                                $newPath = '';
                                if (!$errorGetImage) {
                                    $newPath = '/intuicion/' . $productExist->sku . 'type' . $key . $color->slug . '.jpg';
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
                        } else {
                            $productExist->images()->create([
                                'image_url' => 'img/default_product_image.jpg'
                            ]);
                        }
                    }
                }
            }


            return $result;
        } catch (Exception $e) {
            FailedJobsCron::create([
                'name' => 'Intuicion Publicitaria',
                'message' => $e->getMessage(),
                'status' => 0,
                'type' =>   1
            ]);
            return $e->getMessage();
        }
    }
}
