<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HelperController extends Controller
{
    public function changeProviderToInternalProducts()
    {
        $products = Product::where('type_id', 3)->get();
        foreach ($products as $product) {
            // Obtener el provedor
            $provider = $product->provider->company;
            $product->productAttributes()->create([
                'attribute' => 'Proveedor',
                'slug' => 'proveedor',
                'value' => $provider,
            ]);
        }
    }
}
