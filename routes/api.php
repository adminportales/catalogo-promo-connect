<?php

use App\Http\Controllers\SendProductsToEcommerce;
use App\Models\Category;
use App\Models\Color;
use App\Models\Image;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductCategory;
use App\Models\Provider;
use App\Models\Subcategory;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas de la actualizacion de Web WooCommerce
Route::get('/setProductsToGoodIN', [SendProductsToEcommerce::class, 'setProductsToGoodIN']);

Route::get('/getAllProductos', function () {
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
    ], 200);
});
