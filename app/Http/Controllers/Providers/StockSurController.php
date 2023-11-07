<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\FailedJobsCron;
use App\Models\Product;
use App\Models\Status;
use App\Models\Subcategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockSurController extends Controller
{
    public function getAllProductsStockSur()
    {
        try {

            $result = null;

            $ch = curl_init();
            // Check if initialization had gone wrong*
            curl_setopt(
                $ch,
                CURLOPT_URL,
                "http://api.mexico.cdopromocionales.com/v1/products?auth_token=JQrmMfNK7QE028zBUBMgsQ"
            );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $result = json_decode(curl_exec($ch));
            if ($result == null) {
                Status::create([
                    'name_provider' => 'stockSur',
                    'status' => 'Problemas al acceder al servidor',
                    'update_sumary' => 'No se pudo acceder al servidor de stockSur',
                ]);
                return 'Error';
            }
        } catch (Exception $ex) {
            Status::create([
                'name_provider' => 'stockSur',
                'status' => 'Problemas al acceder al servidor',
                'update_sumary' => 'No se pudo acceder al servidor de stockSur',
            ]);
            return ('Error al acceder al servidor de stockSur');
        }

        try {
            // return $result;
            $maxSKU = Product::max('internal_sku');
            $idSku = null;
            if (!$maxSKU) {
                $idSku = 1;
            } else {
                $idSku = (int) explode('-', $maxSKU)[1];
                $idSku++;
            }
            $dataArregloProductos = array();
            // return $result;
            foreach ($result as $product) {

                $data = [
                    'internal_sku' => "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT),
                    'sku_parent' => $product->code,
                    'name' => $product->name,
                    'description' => $product->description,
                ];

                foreach ($product->variants as $variant) {
                    $color = null;
                    $slug = mb_strtolower(str_replace(' ', '-', $variant->color));
                    $color = Color::where("slug", $slug)->first();
                    if (!$color) {
                        $color = Color::create([
                            'color' => ucfirst($variant->color), 'slug' => $slug,
                        ]);
                    }
                    $data['sku'] = $product->code . '_' . $slug;
                    $data['internal_sku'] = "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT);
                    $data['color_id'] = $color->id;
                    $data['producto_nuevo'] = $variant->novedad;
                    $data['stock'] =  $variant->stock_available;
                    $data['price'] = $variant->list_price;
                    $data['producto_promocion'] = false;
                    $data['precio_unico'] = true;
                    $data['type_id'] = 1;
                    $data['provider_id'] = 6;

                    $productExist = Product::where('sku', $data['sku'])->first();
                    if (!$productExist) {
                        $newProduct = Product::create($data);
                        $newProduct->images()->create([
                            'image_url' => $variant->detail_picture->medium
                        ]);
                        $newProduct->images()->create([
                            'image_url' => $variant->picture->medium
                        ]);
                        $idSku++;
                    } else {
                        $productExist->stock =  $variant->stock_available;
                        $productExist->price = $variant->net_price;
                        $productExist->save();
                        $productExist->images()->delete();
                        $productExist->images()->create([
                            'image_url' => $variant->detail_picture->medium
                        ]);
                        $productExist->images()->create([
                            'image_url' => $variant->picture->medium
                        ]);
                    }
                    array_push($dataArregloProductos, $productExist);
                }
            }

            $allProducts = Product::where('provider_id', 6)->get();
            foreach ($result as $product) {
                foreach ($product->variants as $variant) {
                    $slugNew = mb_strtolower(str_replace(' ', '-', $variant->color));
                    foreach ($allProducts as $key => $value) {
                        if (($value->sku == $product->code . '_' . $slugNew)) {
                            unset($allProducts[$key]);
                            break;
                        }
                    }
                }
            }

            foreach ($allProducts as  $value) {
                $value->visible = 0;
                $value->save();
            }

            /* Status::create([
                'name_provider' => 'stockSur',
                'status' => 'Actualizacion Completa al servidor',
                'update_sumary' => 'Actualizacion Completa de los productos de stockSur',
            ]); */

            DB::table('images')->where('image_url', '=', null)->delete();
            return ($result);
        } catch (Exception $ex) {
            Status::create([
                'name_provider' => 'stockSur',
                'status' => 'Actualización incompleta al servidor',
                'update_sumary' => 'Actualización incompleta de productos del servidor de stockSur',
            ]);

            return ('Actualización incompleta de productos del servidor stockSur');
        }
    }
}
