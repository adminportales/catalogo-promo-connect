<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ResetProducts extends Controller
{
    public function resetProducts() {
        $allProducts = Product::where('provider_id', 1983)->take(10000)->get();

        foreach($allProducts as $product){
            $findProvider = Product::where('sku', $product->sku)->where('provider_id', '<>', 1983)->first();

            if(!$findProvider ){
                $findProvider = Product::where('sku_parent', $product->sku_parent)->where('provider_id', '<>', 1983)->first();
            }

            if($findProvider){
                $product->visible = 0;
                $product->provider_id = $findProvider->provider_id;
                $product->save();
            }
            
        }

        return 'actualizacion de productos exitosa';
    }
}
