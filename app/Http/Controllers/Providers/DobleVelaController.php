<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use Illuminate\Http\Request;

class DobleVelaController extends Controller
{
    public function getAllProductosDoblevela()
    {
        //agregamos la libreria de nusoap del directorio donde se encuentre

        $cliente = new \nusoap_client('http://srv-datos.dyndns.info/doblevela/service.asmx?wsdl', 'wsdl');
        $error = $cliente->getError();
        if ($error) {
            echo 'Error' . $error;
        }
        //agregamos los parametros, en este caso solo es la llave de acceso
        $parametros = array('Key' => 't5jRODOUUIoytCPPk2Nd6Q==');
        //hacemos el llamado del metodo
        $resultado = $cliente->call('GetExistenciaAll', $parametros);

        $products = [];
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

                // return $product;
                $productExist = Product::where('sku', $product->CLAVE)->where('provider_id', 5)->first();
                if (!$productExist) {
                    $newProduct = Product::create([
                        'internal_sku' => "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT),
                        'sku' => $product->CLAVE,
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
                    /*
                    Registrar en la tabla product_category el producto, categoria y sub categoria
                    */
                    $newProduct->productCategories()->create([
                        'category_id' => $categoria->id,
                        'subcategory_id' => $subcategoria->id,
                    ]);

                    /* $attributes = [
                        [
                            'attribute' => 'Modelo',
                            'slug' => 'modelo',
                            'value' => $product->MODELO,
                        ],
                        [
                            'attribute' => 'Nombre Corto',
                            'slug' => 'nombre-corto',
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
                    } */
                    $idSku++;
                    // dd($newProduct);
                } else {
                    $productExist->update([
                        'price' => $product->Price,
                        'stock' => $product->EXISTENCIAS,
                    ]);
                }
            }
        }
    }

    public function getImagesDoblevela()
    {
        //agregamos la libreria de nusoap del directorio donde se encuentre

        $cliente = new \nusoap_client('http://srv-datos.dyndns.info/doblevela/service.asmx?wsdl', 'wsdl');
        $error = $cliente->getError();
        if ($error) {
            echo 'Error' . $error;
        }
        //agregamos los parametros, en este caso solo es la llave de acceso
        $parametros = array('Key' => 't5jRODOUUIoytCPPk2Nd6Q==', 'Codigo' => '{"CLAVES": ["TXM2263"]}');
        //hacemos el llamado del metodo
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
            }
        }

        if ($images != null) {
            // Comenzar a registrar los productos
            $products = Product::where('provider_id', 5)->groupBy('sku_parent')->get();
            dd($products);
            foreach ($products as $product) {

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

                // return $product;
                $productExist = Product::where('sku', $product->CLAVE)->where('provider_id', 5)->first();
                if (!$productExist) {
                    $newProduct = Product::create([
                        'internal_sku' => "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT),
                        'sku' => $product->CLAVE,
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
                    /*
                    Registrar en la tabla product_category el producto, categoria y sub categoria
                    */
                    $newProduct->productCategories()->create([
                        'category_id' => $categoria->id,
                        'subcategory_id' => $subcategoria->id,
                    ]);

                    /* $attributes = [
                        [
                            'attribute' => 'Modelo',
                            'slug' => 'modelo',
                            'value' => $product->MODELO,
                        ],
                        [
                            'attribute' => 'Nombre Corto',
                            'slug' => 'nombre-corto',
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
                    } */
                    dd($newProduct);
                } else {
                    $productExist->update([
                        'price' => $product->Price,
                        'stock' => $product->EXISTENCIAS,
                    ]);
                }
            }
        }
    }
}
