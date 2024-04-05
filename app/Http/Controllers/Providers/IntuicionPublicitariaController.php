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
    
            $productsData = json_decode($result, true)['Data']['Productos'];
    
            foreach ($productsData as $product) {
                $color = Color::firstOrCreate(['slug' => mb_strtolower(str_replace(' ', '-', $product['Color']))], ['color' => ucfirst($product['Color'])]);
    
                $categoria = Category::firstOrCreate(['slug' => mb_strtolower(str_replace(' ', '-', $product['SubGrupo']))], ['family' => ucfirst($product['SubGrupo'])]);

                $subcategoria = $categoria->subcategories()->firstOrCreate(['slug' => mb_strtolower(str_replace(' ', '-', $product['SubGrupo']))], ['subfamily' => ucfirst($product['SubGrupo'])]);

                $productExist = Product::where('sku', $product['Codigo'])->where('color_id', $color->id)->first();
    
                if (!$productExist) {
                    $newProduct = Product::create([
                        'internal_sku' => "PROM-" . str_pad($this->generateSKU(), 7, "0", STR_PAD_LEFT),
                        'sku' => $product['Codigo'],
                        'name' => $product['Marca'] ?? '',
                        'price' => floatval($product['Precio']),
                        'description' => $product['Descripcion'],
                        'stock' => $product['Existencia'],
                        'provider_id' => 1984,
                        'type_id' => 1,
                        'color_id' => $color->id,
                    ]);

                    $newProduct->productCategories()->create([
                        'category_id' => $categoria->id,
                        'subcategory_id' => $subcategoria->id,
                    ]);
    
                    $this->createProductImages($newProduct, $product['Imagenes']);
                    $this->createProductAttributes($newProduct, $product);
                    
                } else {
                    $productExist->update([
                        'price' => floatval($product['Precio']),
                        'stock' => $product['Existencia'],
                    ]);
    
                    if (count($productExist->images) === 0) {
                        $this->createProductImages($productExist, $product['Imagenes']);
                    }
                }
            }
    
            $this->updateProductVisibility($productsData);
    
            DB::table('images')->whereNull('image_url')->delete();
    
            return $result;
        } catch (Exception $e) {
            FailedJobsCron::create([
                'name' => 'Intuicion Publicitaria',
                'message' => $e->getMessage(),
                'status' => 0,
                'type' => 1
            ]);
            return $e->getMessage();
        }
    }
    
    private function generateSKU()
    {
        $maxSKU = Product::max('internal_sku');
        return $maxSKU ? ((int)explode('-', $maxSKU)[1]) + 1 : 1;
    }
    
    private function createProductImages($product, $imageUrls)
    {
        $defaultImageUrl = 'img/default_product_image.jpg';
        
        if (is_array($imageUrls)) {
            foreach ($imageUrls as $imageUrl) {
                $product->images()->create(['image_url' => $imageUrl]);
            }
        } elseif (is_null($imageUrls)) {
            $product->images()->create(['image_url' => $defaultImageUrl]);
        } else {
            $product->images()->create(['image_url' => $defaultImageUrl]);
        }
    }
    
    private function createProductAttributes($product, $data)
    {
        $attributes = [
            ['attribute' => 'Clave SAT del Producto', 'slug' => 'ClaveSAT', 'value' => $data['ClaveSAT']],
            ['attribute' => 'Unidad SAT del Producto', 'slug' => 'UnidadSAT', 'value' => $data['UnidadSAT']],
            ['attribute' => 'Nombre de Lista del Precio del Producto', 'slug' => 'NombreListaPrecio', 'value' => $data['NombreListaPrecio']],
            ['attribute' => 'Unidad de Venta', 'slug' => 'UnidadVenta', 'value' => $data['UnidadVenta']],
            ['attribute' => 'Cantidad Por Paquete', 'slug' => 'CantidadPorPaquete', 'value' => $data['CantidadPorPaquete']],
            ['attribute' => 'Ficha Tecnica del Producto', 'slug' => 'FichaTecnica', 'value' => $data['FichaTecnica']],
        ];
    
        foreach ($attributes as $attr) {
            $product->productAttributes()->create($attr);
        }
    }
    
    private function updateProductVisibility($productsData)
    {
        $allProducts = Product::where('provider_id', 1984)->where('visible', 1)->get();
    
        foreach ($allProducts as $value) {
            $found = false;
            foreach ($productsData as $product) {
                if ($value->sku == $product['Codigo'] && strtolower($value->color->color) == strtolower($product['Color'])) {
                    $found = true;
                    break;
                }
            }
    
            if (!$found) {
                $value->visible = 0;
                $value->provider_id = 1983;
                $value->save();
            }
        }
    }
}
