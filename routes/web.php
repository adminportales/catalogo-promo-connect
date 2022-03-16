<?php

use App\Http\Controllers\ConsultSuppliers;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SendProductsToEcommerce;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();


Route::get('/',  [HomeController::class, 'index'])->middleware(['auth'])->name('home');

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [HomeController::class, 'dashboard']);

    //Route Hooks - Do not delete//
    Route::view('users', 'livewire.users.index');
    Route::view('products', 'livewire.products.index');
    Route::view('subcategories', 'livewire.subcategories.index');
    Route::view('categories', 'livewire.categories.index');
    Route::view('providers', 'livewire.providers.index');
    Route::view('globalAttributes', 'livewire.globalAttributes.index');
});

Route::middleware(['auth'])->group(function () {
    Route::view('/catalogo', 'cotizador.catalogo.index');
});

// Rutas de la actualizacion de Web Services

Route::get('/getAllProductsInnova', [ConsultSuppliers::class, 'getAllProductsInnova']);
Route::get('/getStockInnova', [ConsultSuppliers::class, 'getStockInnova']);

Route::get('/getAllProductsPromoOption', [ConsultSuppliers::class, 'getAllProductsPromoOption']);
Route::get('/getPricePromoOpcion', [ConsultSuppliers::class, 'getPricePromoOpcion']);
Route::get('/getStockPromoOpcion', [ConsultSuppliers::class, 'getStockPromoOpcion']);

Route::get('/getAllProductsForPromotional', [ConsultSuppliers::class, 'getAllProductsForPromotional']);
