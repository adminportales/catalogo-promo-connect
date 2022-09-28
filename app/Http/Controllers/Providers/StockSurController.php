<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\FailedJobsCron;
use App\Models\Product;
use App\Models\Subcategory;
use Exception;
use Illuminate\Http\Request;

class StockSurController extends Controller
{
    public function getAllProductsStockSur()
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
                "http://api.mexico.cdopromocionales.com/v1/products?auth_token=JQrmMfNK7QE028zBUBMgsQ"
            );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $result = json_decode(curl_exec($ch));
            if ($result == null) {
                FailedJobsCron::create([
                    'name' => 'For Promotional',
                    'message' => "HTTP Status 404 – Not Found Metodo No encontrado",
                    'status' => 0,
                    'type' =>   1
                ]);
                return 'Error';
            }

            $maxSKU = Product::max('internal_sku');
            $idSku = null;
            if (!$maxSKU) {
                $idSku = 1;
            } else {
                $idSku = (int) explode('-', $maxSKU)[1];
                $idSku++;
            }
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

                    $productExist = Product::where('sku', $product->code . '_' . $slug)->first();

                    if (!$productExist) {
                        $newProduct = Product::create($data);
                        $newProduct->images()->create([
                            'image_url' => $variant->detail_picture->medium
                        ]);
                        $newProduct->images()->create([
                            'image_url' => $variant->picture->medium
                        ]);
                        $idSku++;
                    }else{
                        $productExist->stock =  $variant->stock_available;
                        $productExist->price = $variant->list_price;
                    }
                }
            }
        } catch (Exception $ex) {
        }
    }
}
