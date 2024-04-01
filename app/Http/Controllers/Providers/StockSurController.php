<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\FailedJobsCron;
use App\Models\Image;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;

class StockSurController extends Controller
{
    public function getAllProductsStockSur(){
        
        try {
            $result = $this->fetchStockSurProducts();

            if ($result === null) {
                FailedJobsCron::create([
                    'name' => 'For Promotional',
                    'message' => "HTTP Status 404 – Not Found Metodo No encontrado",
                    'status' => 0,
                    'type' => 1
                ]);
                return 'Error';
            }
            
            $idSku = $this->getNextInternalSku();
    
            $this->processStockSurProducts($result, $idSku);
    
            // Eliminar imágenes nulas
            Image::whereNull('image_url')->delete();
    
            return $result;
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    private function fetchStockSurProducts(){
        $ch = curl_init();
        // Check if initialization had gone wrong
        if ($ch === false) {
            FailedJobsCron::create([
                'name' => 'For Promotional',
                'message' => "'failed to initialize'",
                'status' => 0,
                'type' => 1
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
        curl_close($ch);

        return $result;
    }

    private function getNextInternalSku(){
        $maxSKU = Product::max('internal_sku');
        $idSku = $maxSKU ? (int)explode('-', $maxSKU)[1] + 1 : 1;
        return $idSku;
    }

    private function processStockSurProducts($result, &$idSku){

        foreach ($result as $product) {
            
            $dataArregloProductos = [];
    
            // Obtener todas las SKU de las variantes de este producto
            $variantSKUs = [];
            foreach ($product->variants as $variant) {
                $slugNew = mb_strtolower(str_replace(' ', '-', $variant->color));
                /* $variantSKUs[] = $product->code . '_' . $slugNew; */
                array_push($variantSKUs,  $product->code . '_' . $slugNew);
        
            }
    
            // Obtener todos los productos existentes con las SKU de las variantes de este producto
/*             $existingProductsAll = Product::whereIn('sku', $variantSKUs)->where('provider_id', 6)->get()->keyBy('sku');
 */        
            foreach ($product->variants as $variant) {
                $slug = mb_strtolower(str_replace(' ', '-', $variant->color));
                $productKey = $product->code . '_' . $slug;
                
                $existingProduct = Product::where('sku', $productKey)->first();
 
                // Verificar si el producto ya existe
                if ($existingProduct) {
                    // Actualizar los detalles del producto existente
                    $existingProduct->stock = $variant->stock_existent;
                    $existingProduct->price = $variant->net_price;
                    $existingProduct->provider_id = 6; 
                    $existingProduct->visible = 1; 
                    $existingProduct->save();
                    $existingProduct->images()->delete();
                    $existingProduct->images()->create([
                        'image_url' => $variant->detail_picture->medium
                    ]);
                    $existingProduct->images()->create([
                        'image_url' => $variant->picture->medium
                    ]);
                    array_push($dataArregloProductos, $existingProduct);
                } else {
                    // Crear un nuevo producto si no existe
                    $color = Color::firstOrCreate(['slug' => $slug], ['color' => ucfirst($variant->color)]);
    
                    $newProductData = [
                        'internal_sku' => "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT),
                        'sku_parent' => $product->code,
                        'sku' => $productKey,
                        'name' => $product->name,
                        'description' => $product->description,
                        'color_id' => $color->id,
                        'producto_nuevo' => $variant->novedad,
                        'stock' =>  $variant->stock_existent,
                        'price' => $variant->net_price,
                        'producto_promocion' => false,
                        'precio_unico' => true,
                        'type_id' => 1,
                        'provider_id' => 6,
                        'visible'=> 1,
                    ];
    
                    $newProduct = Product::create($newProductData);
                    $newProduct->images()->create([
                        'image_url' => $variant->detail_picture->medium
                    ]);
                    $newProduct->images()->create([
                        'image_url' => $variant->picture->medium
                    ]);
                    array_push($dataArregloProductos, $newProduct);
                    $idSku++;
                }
            }
        }
    }

    public function cleanProductsStockSur(){
        $result = $this->fetchStockSurProducts();
    
        // Obtener todas las SKU de los productos de la API
        $apiProductSKUs = [];
        foreach ($result as $stockProduct) {
            foreach ($stockProduct->variants as $variant) {
                $slugNew = mb_strtolower(str_replace(' ', '-', $variant->color));
                $apiProductSKUs[] = $stockProduct->code . '_' . $slugNew;
            }
        }
    
        // Obtener todos los productos existentes

            $repeatedProducts = DB::select("
                SELECT id, sku, color_id
                FROM products
                WHERE provider_id = 6 AND visible = 1 AND sku IN (
                    SELECT sku
                    FROM products
                    WHERE provider_id = 6 AND visible = 1
                    GROUP BY sku
                    HAVING COUNT(*) > 1
                )
            ");
    
            foreach ($repeatedProducts as $product) {
                $productId = $product->id;
                $sku = $product->sku;
                $colorId = $product->color_id;
        
                $firstProductId = DB::selectOne("
                    SELECT MIN(id) AS first_id
                    FROM products
                    WHERE sku = ? AND provider_id = 6 AND visible = 1
                ", [$sku, $colorId])->first_id;
        
                DB::table('products')
                    ->where('sku', $sku)
                    ->where('provider_id', 6)
                    ->where('id', '<>', $firstProductId)
                    ->update(['visible' => 0]);
            }

            DB::commit();
    
        return 'actualización completa';
    }
    
}

    