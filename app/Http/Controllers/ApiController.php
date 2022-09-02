<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\GlobalAttribute;
use App\Models\Image;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductCategory;
use App\Models\Provider;
use App\Models\Subcategory;
use App\Models\Type;

use App\Models\FailedJobsCron;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getAllProducts()
    {
        $providers = Provider::all();
        $categories = Category::all();
        $subcategories = Subcategory::all();
        $types = Type::all();
        $colors = Color::all();
        $products = Product::all();
        $productCategory = ProductCategory::all();
        $productAttribute = ProductAttribute::all();
        $images = Image::all();
        $prices = Price::all();
        $globalAttribute = GlobalAttribute::all();
        return response()->json([
            'providers' => $providers,
            'categories' => $categories,
            'subcategories' => $subcategories,
            'types' => $types,
            'colors' => $colors,
            'products' => $products,
            'productCategory' => $productCategory,
            'productAttribute' => $productAttribute,
            'images' => $images,
            'prices' => $prices,
            'globalAttribute' => $globalAttribute,
        ], 200);
    }
    public function getPricePromoOpcion()
    {
        $products = Product::where('provider_id', 2)->get();
        return response()->json($products);
    }
}
