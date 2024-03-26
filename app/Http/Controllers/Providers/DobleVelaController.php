<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Http\Livewire\Images;
use App\Models\Category;
use App\Models\Color;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\Type\NullType;

use function PHPUnit\Framework\returnSelf;

class DobleVelaController extends Controller
{
    public function getAllProductosDoblevela()
    {
        $cliente = new \nusoap_client('http://srv-datos.dyndns.info/doblevela/service.asmx?wsdl', 'wsdl');
        $error = $cliente->getError();
        if ($error) {
            echo 'Error' . $error;
        }
        //agregamos los parametros, en este caso solo es la llave de acceso
        $parametros = array('Key' => 't5jRODOUUIoytCPPk2Nd6Q==');
        //hacemos el llamado del metodo
        $resultado = $cliente->call('GetExistenciaAll', $parametros);
        if ($error) {
            echo 'Fallo';
            return 0;
        } else {
            $error = $cliente->getError();
            if ($error) {
                echo 'Error' . $error;
                return 0;
            } else {
                // imprimimos el resultado
                $products =  json_decode(utf8_encode($resultado['GetExistenciaAllResult']))->Resultado;
            }
        }

        $productKeys = [];
     
        if (count($products) > 0) {
            // Comenzar a registrar los productos
            $maxSKU = Product::max('internal_sku');
            $idSku = null;
            if (!$maxSKU) {
                $idSku = 1;
            } else {
                $idSku = (int) explode('-', $maxSKU)[1];
                $idSku++;
            }
            foreach ($products as $product) {

                $productKeys[] = $product->CLAVE;

                // Verificar si el color existe y si no registrarla
                $color = null;
                $slug = substr(mb_strtolower(str_replace(' ', '-', $product->COLOR)), 5);
                $color = Color::where("slug", $slug)->first();
                if (!$color) {
                    $color = Color::create([
                        'color' => ucfirst(substr($product->COLOR, 5)), 'slug' => $slug,
                    ]);
                }
                // Verificar si la categoria existe y si no registrarla
                $categoria = null;
                $slug = mb_strtolower(str_replace(' ', '-', $product->Familia));
                $categoria = Category::where("slug", $slug)->first();
                if (!$categoria) {
                    $categoria = Category::create([
                        'family' => ucfirst($product->Familia), 'slug' => $slug,
                    ]);
                }

                // Verificar si la subcategoria existe y si no registrarla
                $subcategoria = null;
                $slugSub = mb_strtolower(str_replace(' ', '-', $product->SubFamilia));
                $subcategoria = $categoria->subcategories()->where("slug", $slugSub)->first();

                if (!$subcategoria) {
                    $subcategoria = $categoria->subcategories()->create([
                        'subfamily' => ucfirst($product->SubFamilia),
                        'slug' => $slugSub,
                    ]);
                }

                // Atributos
                $keysWS = [
                    'Unidad Empaque',
                    "Medida Caja Master",
                    "Peso caja",
                    "Material",
                    "Medida Producto",
                    "Peso Producto",
                    "Tipo Empaque",
                    "Tipo Impresion"
                ];
                $atributos = [
                    [
                        'attribute' => 'Unidad Empaque',
                        'slug' => 'unidad_empaque',
                        'value' => $product->{$keysWS[0]},
                    ],
                    [
                        'attribute' => 'Medida Caja Master',
                        'slug' => 'medida_caja_master',
                        'value' => $product->{$keysWS[1]},
                    ],
                    [
                        'attribute' => 'Peso caja',
                        'slug' => 'peso_caja',
                        'value' => $product->{$keysWS[2]},
                    ],
                    [
                        'attribute' => 'Material',
                        'slug' => 'material',
                        'value' => $product->{$keysWS[3]},
                    ],
                    [
                        'attribute' => 'Medida Producto',
                        'slug' => 'medida_producto',
                        'value' => $product->{$keysWS[4]},
                    ],
                    [
                        'attribute' => 'Peso Producto',
                        'slug' => 'peso_producto',
                        'value' => $product->{$keysWS[5]},
                    ],
                    [
                        'attribute' => 'Tipo Empaque',
                        'slug' => 'tipo_empaque',
                        'value' => $product->{$keysWS[6]},
                    ],
                    [
                        'attribute' => 'Tipo Impresion',
                        'slug' => 'tipo_impresion',
                        'value' => $product->{$keysWS[7]},
                    ]
                ];

                $productExist = Product::where('sku', trim($product->CLAVE))->where('provider_id', 5)->first();
                if (!$productExist) {
                    $newProduct = Product::create([
                        'internal_sku' => "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT),
                        'sku' => trim($product->CLAVE),
                        'sku_parent' => $product->MODELO,
                        'name' => $product->NOMBRE,
                        'price' =>  $product->Price,
                        'description' => $product->Descripcion,
                        'stock' => $product->EXISTENCIAS,
                        'producto_promocion' => false,
                        'descuento' => 0,
                        'producto_nuevo' => false,
                        'precio_unico' => true,
                        'provider_id' => 5,
                        'type_id' => 1,
                        'color_id' => $color->id,
                    ]);
                    foreach ($atributos as $atributo) {
                        $newProduct->productAttributes()->create([
                            'attribute' => $atributo['attribute'],
                            'slug' => $atributo['slug'],
                            'value' => $atributo['value'],
                        ]);
                    }

                    /*
                    Registrar en la tabla product_category el producto, categoria y sub categoria
                    */
                    $newProduct->productCategories()->create([
                        'category_id' => $categoria->id,
                        'subcategory_id' => $subcategoria->id,
                    ]);
                    $idSku++;
                } else {
                    $productExist->update([
                        'price' => $product->Price,
                        'stock' => $product->EXISTENCIAS,
                    ]);
                    foreach ($atributos as $atributo) {
                        // Update or create
                        $productExist->productAttributes()->updateOrCreate(
                            [
                                'slug' => $atributo['slug'],
                            ],
                            [
                                'attribute' => $atributo['attribute'],
                                'value' => $atributo['value'],
                            ]
                        );
                    }
                }
            }

        }
        DB::table('images')->where('image_url', '=', null)->delete();
        return $products;
    }

    public function getImagesDoblevela()
    {
        //agregamos la libreria de nusoap del directorio donde se encuentre

        $cliente = new \nusoap_client('https://srv-datos.dyndns.info/doblevela/service.asmx?wsdl', 'wsdl');
        $error = $cliente->getError();
        if ($error) {
            echo 'Error' . $error;
        }


        // $products = Product::select(['sku_parent'])->where('provider_id', 5)->groupBy('sku_parent')->get();
        $products = Product::where('provider_id', 5)->get();
        /*     return $products; */
        foreach ($products as $product) {
            $color = null;
            $slug = substr(mb_strtolower(str_replace(' ', '-', $product->COLOR)), 5);
            $color = Color::where("slug", $slug)->first();
            if (!$color) {
                $color = Color::create([
                    'color' => ucfirst(substr($product->COLOR, 5)), 'slug' => $slug,
                ]);
            }
            $imagesBD = isset($product->firstImage->image_url) ? $product->firstImage->image_url : null;

            $url = ('https://doblevela.com/images/large' . '/' .  $product->sku_parent . '_' . $product->color->slug . '_' . 'lrg' . '.' . 'jpg');
            if ($imagesBD !== $url || $imagesBD == null) {

                $newImage = Image::create([
                    'image_url' => $url,
                    'product_id' => $product->id,

                ]);
                $newImage->save();
            }
        }


        /*   foreach ($products  as $productInServer) {
            $sku_parent = $productInServer->sku_parent;
            $parametros = array('Key' => 't5jRODOUUIoytCPPk2Nd6Q==', 'Codigo' => '{"CLAVES": ["' . $sku_parent . '"]}');
            $resultado = $cliente->call('GetrProdImagenes', $parametros);
            $images = null;
            if ($error) {
                echo 'Fallo';
                return 0;
            } else {
                $error = $cliente->getError();
                if ($error) {
                    echo 'Error' . $error;
                    return 0;
                } else {
                    // imprimimos el resultado
                    $images =  json_decode(utf8_encode($resultado['GetrProdImagenesResult']))->Resultado;
                    if ($images != null) {
                        // return ($images);
                        // Comenzar a registrar los productos
                        $productsSkuP = Product::where('sku_parent', $sku_parent)->get();
                    }
                }
            }
        } */
        //agregamos los parametros, en este caso solo es la llave de acceso
        //hacemos el llamado del metodo

    }
    public function cleanProductProductosDoblevela()
    {
        $cliente = new \nusoap_client('http://srv-datos.dyndns.info/doblevela/service.asmx?wsdl', 'wsdl');
        $error = $cliente->getError();
        if ($error) {
            echo 'Error' . $error;
        }
        //agregamos los parametros, en este caso solo es la llave de acceso
        $parametros = array('Key' => 't5jRODOUUIoytCPPk2Nd6Q==');
        //hacemos el llamado del metodo
        $resultado = $cliente->call('GetExistenciaAll', $parametros);
        if ($error) {
            echo 'Fallo';
            return 0;
        } else {
            $error = $cliente->getError();
            if ($error) {
                echo 'Error' . $error;
                return 0;
            } else {
                // imprimimos el resultado
                $products =  json_decode(utf8_encode($resultado['GetExistenciaAllResult']))->Resultado;
            }
        }

        if (count($products) > 0) {

            $DBproducts = Product::where('provider_id',5)->get();

            foreach ($DBproducts as $DBproduct) {
                $found = false; // Variable para indicar si se encuentra el producto

                foreach ($products as $product) {
                    if ($product->CLAVE == $DBproduct->sku) {
                        $found = true; // Se encontró el producto
                        break; // Salir del bucle
                    }
                }

                if ($found) {
                    // Si se encontró, no es necesario hacer nada, seguirá visible
                } else {
                    $DBproduct->provider_id = 1983;
                    $DBproduct->visible = 0; // Si no se encontró, marcar como no visible y cambiar el proveedor
                    $DBproduct->save(); // Guardar los cambios en la base de datos
                }
            }

            // Obtener los SKU de los productos repetidos para el proveedor ID 2
            $repeatedSkus = DB::select("
            SELECT sku
            FROM products
            WHERE provider_id = 5 
            GROUP BY sku
            HAVING COUNT(*) > 1
            ");

            foreach ($repeatedSkus as $repeatedSku) {
                $sku = $repeatedSku->sku;

                // Obtener el primer producto de cada SKU repetido para el proveedor ID 2
                $firstProductId = DB::selectOne("
                    SELECT MIN(id) AS first_id
                    FROM products
                    WHERE sku = ? AND provider_id = 5 AND visible = 1
                ", [$sku])->first_id;

                // Cambiar la visibilidad a 0 para los productos repetidos, excepto el primero
                DB::table('products')
                    ->where('sku', $sku)
                    ->where('provider_id', 5)
                    ->where('visible', 1)
                    ->where('id', '<>', $firstProductId)
                    ->update(['visible' => 0]);
            }

            DB::commit();

        }
    }
}
