<?php

namespace App\Http\Controllers\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;
use App\Models\Category;
use App\Models\Color;
use App\Models\FailedJobsCron;
use App\Models\Product;
use App\Models\Status;
use App\Http\Controllers\Controller;

class EuroCottonController extends Controller
{
    public function getAllProductsEurocotton()
    {
        try {
            $result = null;
            $ch = curl_init();

            curl_setopt(
                $ch,
                CURLOPT_URL,
                "https://ac.appeuro.mx/whugebservices/"
            );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $result = curl_exec($ch);

            if (strpos($result, "HTTP Status 404 – Not Found") == true) {
                Status::create([
                    'name_provider' => 'Eurocotton',
                    'status' => 'Problemas al acceder al servidor',
                    'update_sumary' => 'No se pudo acceder al servidor de Eurocotton',
                ]);
                return 'Error';
            }

            // Check the return value of curl_exec(), too
            if ($result === false) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }
        } catch (Exception $e) {
            Status::create([
                'name_provider' => 'Eurocotton',
                'status' => 'Problemas al acceder al servidor',
                'update_sumary' => 'No se pudo acceder al servidor de Eurocotton',
            ]);
            return ('Error al acceder al servidor de Eurocotton');
        }

        try {
            $arrContextOptions = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );
            // Convertir en array
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
                // dd($products); //ver productos
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
                $slugSub = mb_strtolower(str_replace(' ', '-', $product['subcategoria']));
                $subcategoria = $categoria->subcategories()->where("slug", $slugSub)->first();

                if (!$subcategoria) {
                    $subcategoria = $categoria->subcategories()->create([
                        'subfamily' => ucfirst($product['subcategoria']),
                        'slug' => $slugSub,
                    ]);
                }

                $discount = $product['producto_promocion'] == "SI" ? $product['desc_promo'] : 0;

                $productExist = Product::where('sku', $product['id_articulo'])->where('color_id', $color->id)->first();
                if (!$productExist) {
                    $newProduct = Product::create([
                        'internal_sku' => "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT),
                        'sku' => $product['id_articulo'],
                        'name' => $product['nombre_articulo'],
                        'price' =>  $product['precio'],
                        'description' => $product['descripcion'],
                        'stock' => $product['inventario'],
                        'producto_promocion' => $product['producto_promocion'] == "SI" ? true : false,
                        'descuento' => $discount,
                        'producto_nuevo' => $product['producto_nuevo'] == "SI" ? true : false,
                        'precio_unico' => true,
                        'provider_id' => 9,
                        'type_id' => 1,
                        'color_id' => $color->id,
                    ]);
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
                        ],
                        [
                            'attribute' => 'Piezas de la caja',
                            'slug' => 'piezas_caja',
                            'value' => $product['piezas_caja'],
                        ],
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
                    ]);
                }
            }

            // Status::create([
            //     'name_provider' => 'Eurocotton',
            //     'status' => 'Actualizacion Completa al servidor',
            //     'update_sumary' => 'Actualizacion Completa de los productos de Eurocotton',
            // ]);

            DB::table('images')->where('image_url', '=', null)->delete();

            return $products;
        } catch (Exception) {
            Status::create([
                'name_provider' => 'Eurocotton',
                'status' => 'Actualización incompleta al servidor',
                'update_sumary' => 'Actualización incompleta del productos del servidor de Eurocotton',
            ]);

            return ('Actualización incompleta de productos del servidor de Eurocotton');
        }

        // $allProducts = Product::where('provider_id', 9)->get();
        // foreach ($products as $product) {
        //     foreach ($allProducts as $key => $value) {
        //         if ($value->sku == $product['id_articulo'] && strtolower($value->color->color) == strtolower($product['color'])) {
        //             break;
        //         }
        //     }
        //     unset($allProducts[$key]);
        // }

        // foreach ($allProducts as  $value) {
        //     $value->visible = 0;
        //     $value->save();
        // }

        // $allProducts = Product::where('provider_id', 9)->where('visible', 1)->get();
        // foreach ($allProducts as $key => $value) {
        //     foreach ($products as $product) {
        //         if ($value->sku == $product['id_articulo'] && strtolower($value->color->color) == strtolower($product['color'])) {
        //             unset($allProducts[$key]);
        //             break;
        //         }
        //     }
        // }
        // foreach ($allProducts as  $value) {
        //     $value->visible = 0;
        //     $value->save();
        // }



        // } catch (Exception $e) {
        //     FailedJobsCron::create([
        //         'name' => 'For Promotional',
        //         'message' => $e->getMessage(),
        //         'status' => 0,
        //         'type' =>   1
        //     ]);
        //     return $e->getMessage();
        // }
    }
}
