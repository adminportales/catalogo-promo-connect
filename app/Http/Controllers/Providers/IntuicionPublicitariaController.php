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
        try {
            $response = Http::post('http://138.118.8.25:8090/api/Inventario/Acceso', [
                "Username" => "ITFACTORY",
                "Password" => "123456",
            ]);

            $result = $response->body();

            if ($response->status() === 404) {
                FailedJobsCron::create([
                    'name' => 'Intuicion Publicitaria',
                    'message' => "HTTP Status 404 – Not Found Metodo No encontrado",
                    'status' => 0,
                    'type' => 1
                ]);
                return 'Error';
            }

            // Convertir en array
            // $products = json_decode($result, true);
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

                $color = null;
                $product['Color'];
                $slug = mb_strtolower(str_replace(' ', '-', $product['Color']));
                $color = Color::where("slug", $slug)->first();

                if (!$color) {
                    $color = Color::create([
                        'color' => ucfirst($product['Color']), 'slug' => $slug,
                    ]);
                }

                $categoria = null;
                $slug = mb_strtolower(str_replace(' ', '-', $product['SubGrupo']));
                $categoria = Category::where("slug", $slug)->first();
                if (!$categoria) {
                    $categoria = Category::create([
                        'family' => ucfirst($product['SubGrupo']), 'slug' => $slug,
                    ]);
                }

                $subcategoria = null;
                $slugSub = mb_strtolower(str_replace(' ', '-', $product['SubGrupo']));
                $subcategoria = $categoria->subcategories()->where("slug", $slugSub)->first();

                if (!$subcategoria) {
                    $subcategoria = $categoria->subcategories()->create([
                        'subfamily' => ucfirst($product['SubGrupo']),
                        'slug' => $slugSub,
                    ]);
                }

                $productExist = Product::where('sku', $product['Codigo'])->where('color_id', $color->id)->first();
                if (!$productExist) {
                    $newProduct = Product::create([
                        'internal_sku' => "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT),
                        'sku' => $product['Codigo'],
                        'name' => $product['Marca'],
                        'price' => floatval($product['Precio']),
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

                    $imagenes = $product['Imagenes'];

                    if (is_array($imagenes)) {
                        foreach ($imagenes as $key => $imagen) {
                            // Descargar Imagenes solo Si no existen
                            $errorGetImage = false;
                            $fileImage = "";
                            try {
                                $fileImage = file_get_contents(str_replace(' ', '%20', $imagen), false);
                            } catch (Exception $th) {
                                $errorGetImage = true;
                            }
                            $newPath = '';
                            if (!$errorGetImage) {
                                $newPath = '/intuicion/' . $product['NombreImagen'];
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
                    } elseif (is_null($imagenes)) {
                        // Si no hay imágenes, usar la imagen por defecto
                        $newProduct->images()->create(['image_url' => 'img/default_product_image.jpg']);
                    } 

                    // $imagenes = $product['Imagenes'];

                    // if ($imagenes != null) {
                    //     foreach ($imagenes as $imagen) {
                    //         $newProduct->images()->create([
                    //             'image_url' => $imagen
                    //         ]);
                    //     }
                    // }

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
                } else {
                    $productExist->update([
                        'price' => floatval($product['Precio']),
                        'stock' => $product['Existencia'],
                    ]);
                    $productExist->images()->delete();
                    $imagenes = $product['Imagenes'];

                    if (is_array($imagenes)) {
                        foreach ($imagenes as $key => $imagen) {
                            // Descargar Imagenes solo Si no existen
                            $errorGetImage = false;
                            $fileImage = "";
                            try {
                                $fileImage = file_get_contents(str_replace(' ', '%20', $imagen), false);
                            } catch (Exception $th) {
                                $errorGetImage = true;
                            }
                            $newPath = '';
                            if (!$errorGetImage) {
                                $newPath = '/intuicion/' . $product['NombreImagen'];
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
                    } elseif (is_null($imagenes)) {
                        // Si no hay imágenes, usar la imagen por defecto
                        $productExist->images()->create(['image_url' => 'img/default_product_image.jpg']);
                    } 
                }
            }

            $allProducts = Product::where('provider_id', 1984)->get();
            foreach ($products as $product) {
                foreach ($allProducts as $key => $value) {
                    if ($value->sku == $product['Codigo'] && strtolower($value->color->color) == strtolower($product['Color'])) {
                        break;
                    }
                }
                unset($allProducts[$key]);
            }

            foreach ($allProducts as  $value) {
                $value->visible = 1;
                $value->save();
            }

            $allProducts = Product::where('provider_id', 1984)->where('visible', 1)->get();
            foreach ($allProducts as $key => $value) {
                foreach ($products as $product) {
                    if ($value->sku == $product['Codigo'] && strtolower($value->color->color) == strtolower($product['Color'])) {
                        unset($allProducts[$key]);
                        break;
                    }
                }
            }

            DB::table('images')->where('image_url', '=', null)->delete();
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
