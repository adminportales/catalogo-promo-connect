<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\FailedJobsCron;
use App\Models\Product;
use App\Models\Subcategory;
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
                'type' => 1
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
                    // [
                    //     'attribute' => 'Capacidad',
                    //     'slug' => 'capacity',
                    //     'value' => $product['capacidad'],
                    // ],
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
                    [
                        'attribute' => 'Tipo Descuento',
                        'slug' => 'discount_type',
                        'value' => isset($product['hijos'][0]['tipo']) ? $product['hijos'][0]['tipo'] : '',
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
                    // Actualizar los atributos

                    //Create or update
                    $atrr = [
                        [
                            'attribute' => 'Piezas Inner',
                            'slug' => 'piezas_inner',
                            'value' => $product['paquete']["PiezasInner"],
                        ],
                        [
                            'attribute' => 'Piezas de la caja',
                            'slug' => 'piezas_caja',
                            'value' => $product['paquete']["PiezasCaja"],
                        ],
                        [
                            'attribute' => 'Tipo Descuento',
                            'slug' => 'discount_type',
                            'value' => isset($product['hijos'][0]['tipo']) ? $product['hijos'][0]['tipo'] : '',
                        ],
                    ];

                    foreach ($atrr as $attribute) {
                        // create or update
                        $productExist->productAttributes()->updateOrCreate(
                            [
                                'slug' => $attribute['slug'],
                            ],
                            [
                                'attribute' => $attribute['attribute'],
                                'value' => $attribute['value'],
                            ]
                        );
                    }


                    $productExist->price = $productHijo['precio'];
                    $productExist->visible = 1;
                    $productExist->save();
                    $imagenes =  count($productHijo['imagenesHijo']) <= 0 ? $product['imagenesPadre'] : $productHijo['imagenesHijo'];
                    $productExist->images()->delete();
                    foreach ($imagenes as $image) {
                        $productExist->images()->create([
                            'image_url' => $image
                        ]);
                    }
                }
            }
        }

        // Obtener los productos que estan en la base de datos y no en el proveedor
        $allProducts = Product::where('provider_id', 2)->get(['id', 'sku']);

        $dataSkus = [];
        foreach ($result['response'] as $value) {
            foreach ($value['hijos'] as $hijo) {
                if ($hijo['estatus'] == 0 || $hijo['estatus'] == '') {
                    // Romper aqui y continuar con el siguiente ciclo del foreach
                    continue;
                }
                array_push($dataSkus, ["sku" => $hijo['skuHijo']]);
            }
        }

        // Buscar los productos que no estan en el proveedor
        $allProducts = Product::where('provider_id', null)->get(['id', 'sku_parent']);
        foreach ($allProducts as $key => $value) {
            $found = false;
            foreach ($dataSkus as $skuProvider) {
                if ($value->sku == $skuProvider['sku']) {
                    $found = true;
                    break;
                }
            }
            if ($found) {
                $value->provider_id = 2;
                $value->visible = 1;
            } else {
                $value->visible = 0;
            }
            $value->save();
        }
        //buscar productos que estan en el provedor pero ya no estan disponibles y ponerle 0
        $newProducts = Product::where('provider_id', 2)->get(['id', 'sku_parent']);
        foreach ($newProducts as $key => $value) {

            foreach ($dataSkus as $skuProvider) {
                return $skuProvider;
                if ($value->sku_parent == $skuProvider['sku']) {
                    $value->provider_id = 2;
                    $value->visible = 1;
                    $value->save();
                    break;
                } else {
                    $value->visible = 0;
                    $value->save();
                }
            }
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

        $coleccionDatos = collect($stocks);

        $newStocks = $coleccionDatos->groupBy('Material')->map(function ($items) {
            /* return $items->all(); */
            return [
                "Material" => $items[0]["Material"],
                "Stock" => $items->sum('Stock'),
                "Planta" => $items[0]["Planta"],
            ];
        })->values()->all();

        // return $response;
        $errors = [];

        foreach ($newStocks as $stock) {
            $productCatalogo = Product::where('sku', $stock['Material'])->first();
            if ($productCatalogo) {
                $productCatalogo->stock = $stock['Stock'];
                $productCatalogo->save();
            } else {
                array_push($errors, $stock['Material']);
            }
        }
        return [$errors, $stocks];

        FailedJobsCron::create([
            'name' => 'Promo Opcion',
            'message' => "Productos No encontrados al actualizar el precio: " . implode(",", $errors),
            'status' => 0,
            'type' =>   1
        ]);

        return $errors;
    }
}
