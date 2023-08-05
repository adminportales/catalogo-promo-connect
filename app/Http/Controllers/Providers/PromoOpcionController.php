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
        $passowrd = "5MrZtuzmiiuwSswLuONi";
        $postFields = [
            'user' => $user,
            'password' => $passowrd,
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields); //Opcional
        curl_setopt(
            $ch,
            CURLOPT_URL,
            "https://promocionalesenlinea.net/api/all-products"
        );
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result, true);
        if (!isset($result['success'])) {
            return $result;
        }
        if (!$result['success'] == true) {
            return $result;
        }

        if (!isset($result['response'])) {
            return $result;
        }
        $productsWs =  $result['response'];
        $maxSKU = Product::max('internal_sku');
        $idSku = null;
        if (!$maxSKU) {
            $idSku = 1;
        } else {
            $idSku = (int) explode('-', $maxSKU)[1];
            $idSku++;
        }

        foreach ($productsWs as $product) {
            // Verificar si la categoria existe y si no registrarla
            $categoria = null;
            $slug = mb_strtolower(str_replace(' ', '-', $product['categorias']));
            $categoria = Category::where("slug", $slug)->first();
            if (!$categoria) {
                $categoria = Category::create([
                    'family' => ucfirst($product['categorias']), 'slug' => $slug,
                ]);
            }
            // Verificar si la subcategoria existe y si no registrarla
            $subcategoria = Subcategory::find(1);

            $data = [
                'sku_parent' => $product['skuPadre'],
                'description' => $product['descripcion'],
                'stock' => 0,
                'producto_promocion' => false,
                'producto_nuevo' => false,
                'precio_unico' => true,
                'type_id' => 1,
                'provider_id' => 2,
            ];
            foreach ($product['hijos'] as $productHijo) {
                $color = null;
                if ($productHijo['color'] == null) {
                    $color = Color::find(1);
                } else {
                    $slug = mb_strtolower(str_replace(' ', '-', $productHijo['color']));
                    $color = Color::where("slug", $slug)->first();
                    if (!$color) {
                        $color = Color::create([
                            'color' => ucfirst($productHijo['color']), 'slug' => $slug,
                        ]);
                    }
                }
                $data['color_id'] = $color->id;
                $attributes = [
                    [
                        'attribute' => 'Tipo',
                        'slug' => 'type',
                        'value' => $productHijo['tipo'],
                    ],
                    [
                        'attribute' => 'Talla',
                        'slug' => 'size',
                        'value' => $productHijo['talla'],
                    ],
                    [
                        'attribute' => 'Material',
                        'slug' => 'material',
                        'value' => $product['material'],
                    ],
                    [
                        'attribute' => 'Capacidad',
                        'slug' => 'capacity',
                        'value' => $product['capacidad'],
                    ],
                    // Medidas
                    [
                        'attribute' => 'Medidas',
                        'slug' => 'medidas',
                        'value' => $product['medidas'],
                    ],
                    [
                        'attribute' => 'Impresion',
                        'slug' => 'printing',
                        'value' => $product['impresion']['tecnicaImpresion'],
                    ],
                    [
                        'attribute' => 'Area de impresion',
                        'slug' => 'printing_area',
                        'value' => $product['impresion']['areaImpresion'],
                    ],
                ];
                $data['price'] =  $productHijo['precio'];
                $data['name'] = $productHijo['nombreHijo'];
                $data['sku'] = $productHijo['skuHijo'];
                $data['internal_sku'] = "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT);
                $productExist = Product::where('sku', $productHijo['skuHijo'])->first();
                if (!$productExist) {
                    if ($productHijo['estatus'] == 0 || $productHijo['estatus'] == '') {
                        // Romper aqui y continuar con el siguiente ciclo del foreach
                        continue;
                    }
                    $newProduct = Product::create($data);
                    $imagenes =  count($productHijo['imagenesHijo']) <= 0 ? $product['imagenesPadre'] : $productHijo['imagenesHijo'];
                    foreach ($imagenes as $image) {
                        $newProduct->images()->create([
                            'image_url' => $image
                        ]);
                    }
                    $newProduct->productCategories()->create([
                        'category_id' => $categoria->id,
                        'subcategory_id' => $subcategoria->id,
                    ]);
                    foreach ($attributes as $attr) {
                        $newProduct->productAttributes()->create($attr);
                    }
                    $idSku++;
                } else {
                    // Actualizar el precio
                    $productExist->price = $productHijo['precio'];
                    $productExist->save();
                }
            }
        }

        $allProducts = Product::where('provider_id', 2)->get();
        foreach ($allProducts as $key => $value) {
            foreach ($productsWs as $product) {
                foreach ($product['hijos'] as $productHijo) {
                    if ($value->sku == $productHijo['skuHijo'] && $productHijo['estatus'] == '1') {
                        unset($allProducts[$key]);
                        break 2;
                    }
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

    public function getStockPromoOpcion()
    {
        $user = "DFE4516";
        $passowrd = "5MrZtuzmiiuwSswLuONi";
        $postFields = [
            'user' => $user,
            'password' => $passowrd,
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields); //Opcional
        curl_setopt(
            $ch,
            CURLOPT_URL,
            "https://promocionalesenlinea.net/api/all-stocks"
        );
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result, true);
        if (!isset($result['success'])) {
            return $result;
        }
        if (!$result['success'] == true) {
            return $result;
        }

        if (!isset($result['Stocks'])) {
            return $result;
        }
        // Convertir en array
        $stocks = $result['Stocks'];

        // return $response;
        $errors = [];

        foreach ($stocks as $stock) {
            $productCatalogo = Product::where('sku', $stock['Material'])->first();
            if ($productCatalogo) {
                $productCatalogo->update(['stock' => $stock['Stock']]);
            } else {
                array_push($errors, $stock['Material']);
            }
        }
        return $errors;

        FailedJobsCron::create([
            'name' => 'Promo Opcion',
            'message' => "Productos No encontrados al actualizar el precio: " . implode(",", $errors),
            'status' => 0,
            'type' =>   1
        ]);

        return $errors;
    }
}
