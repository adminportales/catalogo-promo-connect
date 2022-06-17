<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\SendProductsToEcommerce;
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
// Route::get('/setProductsToGoodIN', [SendProductsToEcommerce::class, 'setProductsToGoodIN']);

Route::get('/getAllProductos',  [ApiController::class, 'getAllProducts']);
Route::get('/getPricePromoOpcion',  [ApiController::class, 'getPricePromoOpcion']);
