<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\FailedJobsCron;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Exchange_Rate;
use Illuminate\Support\Facades\DB;

class PromoOpcionGMTController extends Controller
{
    private $user;
    private $password;
    private $provider_id;

    public function __construct()
    {
        $this->user = "GTM0316";
        $this->password = "xQEmXL2QHNg5h9iiewA5";
        $this->provider_id = 1987;
    }

    public function getAllProductsPromoOption()
    {

        $exist_exchange_rate = Exchange_Rate::where('currency_from', 'GTQ')
            ->where('currency_to', 'MXN')
            ->first();
        $rate = $exist_exchange_rate ? $exist_exchange_rate->rate : 1;

        $postFields = [
            'user' => $this->user,
            'password' => $this->password,
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
                'provider_id' => $this->provider_id,
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
                $data['price'] =  $productHijo['precio']  * $rate;
                $data['name'] = $productHijo['nombreHijo'];
                $data['sku'] = $productHijo['skuHijo'];
                $data['internal_sku'] = "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT);
                $productExist = Product::where('sku', $productHijo['skuHijo'])->where('provider_id', $this->provider_id)->first();
                if (!$productExist) {
                    if ($productHijo['estatus'] == 0 || $productHijo['estatus'] == '') {
                        $data['visible'] = 0;
                    } else {
                        $data['visible'] = 1;
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

                    $visible = 1;
                    if ($productHijo['estatus'] == 0 || $productHijo['estatus'] == '') {
                        $visible = 0;
                    }

                    $productExist->price = $productHijo['precio'] * $rate;
                    $productExist->visible = $visible;
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

        DB::table('images')->where('image_url', '=', null)->delete();

        return $result;
    }

    public function getStockPromoOpcion()
    {
        $postFields = [
            'user' => $this->user,
            'password' => $this->password,
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

    public function cleanStockPromoOpcion()
    {

        $postFields = [
            'user' => $this->user,
            'password' => $this->password,
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

        //Todos los productos de la base de datos
        $allProducts = Product::where('provider_id', $this->provider_id)->get();

        foreach ($allProducts as $dbproduct) {
            $found = false; // Variable para indicar si se encuentra el producto en los hijos

            foreach ($productsWs as $product) {
                foreach ($product['hijos'] as $productHijo) {
                    if ($productHijo['skuHijo'] == $dbproduct->sku) {
                        $found = true; // Se encontró el producto en los hijos
                        break 2; // Salir de ambos bucles foreach
                    }
                }
            }

            if ($found) {
                $dbproduct->visible = 1; // Si se encontró, marcar como visible
            } else {
                $dbproduct->provider_id = 1983;
                $dbproduct->visible = 0; // Si no se encontró, marcar como no visible
            }

            $dbproduct->save();
        }

        // Obtener los SKU de los productos repetidos para el proveedor ID 2
        $repeatedSkus = DB::select("
        SELECT sku
        FROM products
        WHERE provider_id = ?
        GROUP BY sku
        HAVING COUNT(*) > 1
        ", [$this->provider_id]);

        foreach ($repeatedSkus as $repeatedSku) {
            $sku = $repeatedSku->sku;

            // Obtener el primer producto de cada SKU repetido para el proveedor ID 2
            $firstProductId = DB::selectOne("
            SELECT MIN(id) AS first_id
            FROM products
            WHERE sku = ? AND provider_id = ? AND visible = 1
        ", [$sku, $this->provider_id])->first_id;

            // Cambiar la visibilidad a 0 para los productos repetidos, excepto el primero
            DB::table('products')
                ->where('sku', $sku)
                ->where('provider_id', $this->provider_id)
                ->where('visible', 1)
                ->where('id', '<>', $firstProductId)
                ->update(['visible' => 0]);
        }

        DB::commit();

        DB::table('images')->where('image_url', '=', null)->delete();

        return $result;
    }
}
