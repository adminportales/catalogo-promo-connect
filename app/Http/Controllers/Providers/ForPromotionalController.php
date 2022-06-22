<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\FailedJobsCron;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

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
                "https://forpromotional.homelinux.com:9090/WsEstrategia/inventario"
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
                        'provider_id' => 1,
                        'type_id' => 1,
                        'color_id' => $color->id,
                    ]);
                    foreach (array_reverse($product['imagenes']) as $imagen) {
                        $newProduct->images()->create([
                            'image_url' => $imagen['url_imagen']
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
            return $products;
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

    public function apiGetProductsFP()
    {
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
            "https://forpromotional.homelinux.com:9090/WsEstrategia/inventario"
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
        $products = json_decode($result, true);
        return response()->json($products);
    }

    public function getAllProductsForPromotionalToOtherServer()
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
            $url_server = 'https://dev-intranet.promolife.lat/';
            curl_setopt(
                $ch,
                CURLOPT_URL,
                "{$url_server}/api/getProductsFP"
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
                        'provider_id' => 1,
                        'type_id' => 1,
                        'color_id' => $color->id,
                    ]);
                    foreach (array_reverse($product['imagenes']) as $imagen) {
                        $newProduct->images()->create([
                            'image_url' => $imagen['url_imagen']
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
            return $products;
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
}