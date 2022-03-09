<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SendProductsToEcommerce extends Controller
{
    public function setAllProducts()
    {
        $products = Product::where('ecommerce', 1)->get();

        return $products;
    }
    public function updateAllProducts()
    {
        # code...
    }
}
