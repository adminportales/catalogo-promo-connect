<?php

use App\Http\Controllers\BatchInputProducts;
use App\Http\Controllers\ConsultSuppliers;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Providers\DobleVelaController;
use App\Http\Controllers\Providers\ForPromotionalController;
use App\Http\Controllers\Providers\InnovationController;
use App\Http\Controllers\Providers\IUSBController;
use App\Http\Controllers\Providers\PromoOpcionController;
use App\Http\Controllers\Providers\StockSurController;
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
Route::get('/loginEmail', [LoginController::class, 'loginWithLink'])->name('loginWithLink');


Route::get('/',  [HomeController::class, 'index'])->middleware(['auth'])->name('home');

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [HomeController::class, 'dashboard']);

    //Route Hooks - Do not delete//
    Route::view('prices', 'livewire.prices.index')->middleware('auth');
    Route::view('product_attributes', 'livewire.product_attributes.index')->middleware('auth');
    Route::view('products', 'livewire.products.index');

    Route::view('batchInputProducts',  'livewire.products.importProducts');
    Route::post('batchInputProducts/iusb',  [BatchInputProducts::class, 'updateProductsIUSB'])->name('import.iusb');
    Route::view('sites', 'livewire.sites.index')->middleware('auth');
    Route::view('users', 'livewire.users.index');
    Route::view('subcategories', 'livewire.subcategories.index');
    Route::view('categories', 'livewire.categories.index');
    Route::view('providers', 'livewire.providers.index');
    Route::view('globalAttributes', 'livewire.globalAttributes.index');
});

Route::middleware(['auth'])->group(function () {
    Route::view('/catalogo', 'cotizador.catalogo.index');
});

// Rutas de la actualizacion de Web Services

// Innova
Route::get('/getAllProductsInnova', [InnovationController::class, 'getAllProductsInnova']);
Route::get('/getStockInnova', [InnovationController::class, 'getStockInnova']);

// PromoOpcion
Route::get('/getAllProductsPromoOption', [PromoOpcionController::class, 'getAllProductsPromoOption']);
Route::get('/getPricePromoOpcion', [PromoOpcionController::class, 'getPricePromoOpcion']);
Route::get('/getStockPromoOpcion', [PromoOpcionController::class, 'getStockPromoOpcion']);

// ForPromotional
Route::get('/getAllProductsForPromotional', [ForPromotionalController::class, 'getAllProductsForPromotional']);
Route::get('/getAllProductsForPromotionalToOtherServer', [ForPromotionalController::class, 'getAllProductsForPromotionalToOtherServer']);

// IUSB
Route::get('/getStockIUSB', [IUSBController::class, 'getStockIUSB']);

// Doble Vela
Route::get('/getAllProductosDoblevela', [DobleVelaController::class, 'getAllProductosDoblevela']);
Route::get('/getImagesDoblevela', [DobleVelaController::class, 'getImagesDoblevela']);

// StockSur
Route::get('/getProductsStockSur', [StockSurController::class, 'getAllProductsStockSur']);
