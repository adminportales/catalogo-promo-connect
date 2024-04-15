<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BatchInputProducts;
use App\Http\Controllers\ConsultSuppliers;
use App\Http\Controllers\Providers\DKPSController;
use App\Http\Controllers\HelperController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Providers\DobleVelaController;
use App\Http\Controllers\Providers\ForPromotionalController;
use App\Http\Controllers\Providers\EuroCottonController;
use App\Http\Controllers\Providers\G4Controller;
use App\Http\Controllers\Providers\ImpressLineController;
use App\Http\Controllers\Providers\InnovationController;
use App\Http\Controllers\Providers\IntuicionPublicitariaController;
use App\Http\Controllers\Providers\IUSBController;
use App\Http\Controllers\Providers\PromoOpcionController;
use App\Http\Controllers\Providers\StockSurController;
use App\Http\Controllers\ResetProducts;
use App\Http\Controllers\SendProductsToEcommerce;
use App\Http\Controllers\SettingsController;
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

Route::get('/loginEmail', [LoginController::class, 'loginWithLink'])->name('loginWithLink');
Route::get('/loginPunchOut',  [ApiController::class, 'loginPunchOut']);

Auth::routes();


Route::get('/',  [HomeController::class, 'index'])->middleware(['auth'])->name('home');
Route::get('/setRoles', [SettingsController::class, 'setRoles']);

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [HomeController::class, 'dashboard']);

    //Route Hooks - Do not delete//
    Route::view('media', 'livewire.mediums.index')->middleware('auth')->name('media.index');
    Route::view('prices', 'livewire.prices.index')->middleware('auth');
    Route::view('product_attributes', 'livewire.product_attributes.index')->middleware('auth');
    Route::view('products', 'livewire.products.index');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');

    Route::view('batchInputProducts',  'livewire.products.importProducts');
    Route::view('batchInputDoblevela',  'livewire.products.importDobleVela');
    Route::post('batchInputProducts/iusb',  [BatchInputProducts::class, 'updateProductsIUSB'])->name('import.iusb');
    Route::view('sites', 'livewire.sites.index')->middleware('auth');
    Route::view('users', 'livewire.users.index');
    Route::view('subcategories', 'livewire.subcategories.index');
    Route::view('categories', 'livewire.categories.index');
    Route::view('providers', 'livewire.providers.index');
    Route::view('globalAttributes', 'livewire.globalAttributes.index');

    Route::view('roles-providers', 'livewire.roles-providers.index');
    Route::get('/export-data', [HomeController::class, 'obtenerProductos']);
});

Route::middleware(['auth'])->group(function () {
    Route::view('/catalogo', 'cotizador.catalogo.index');
});

// Rutas de la actualizacion de Web Services

// Innova
Route::get('/getAllProductsInnova', [InnovationController::class, 'getAllProductsInnova']);
Route::get('/getStockInnova', [InnovationController::class, 'getStockInnova']);
Route::get('/cleanAllProductsInnova', [InnovationController::class, 'cleanAllProductsInnova']);


// PromoOpcion
Route::get('/getAllProductsPromoOption', [PromoOpcionController::class, 'getAllProductsPromoOption']);
Route::get('/getPricePromoOpcion', [PromoOpcionController::class, 'getPricePromoOpcion']);
Route::get('/getStockPromoOpcion', [PromoOpcionController::class, 'getStockPromoOpcion']);
Route::get('/cleanStockPromoOpcion', [PromoOpcionController::class, 'cleanStockPromoOpcion']);

// ForPromotional
Route::get('/getAllProductsForPromotional', [ForPromotionalController::class, 'getAllProductsForPromotional']);
Route::get('/cleanAllProductsForPromotional', [ForPromotionalController::class, 'cleanAllProductsForPromotional']);

// Route::get('/getAllProductsForPromotionalToOtherServer', [ForPromotionalController::class, 'getAllProductsForPromotionalToOtherServer']);

//DKSP
Route::get('/getAllProductsDKSP', [DKPSController::class, 'getAllProductsDKSP']);
// IUSB
Route::get('/getStockIUSB', [IUSBController::class, 'getStockIUSB']);

// Doble Vela
Route::get('/getAllProductosDoblevela', [DobleVelaController::class, 'getAllProductosDoblevela']);
Route::get('/getProductProductosDoblevela/{sku}', [DobleVelaController::class, 'getProductProductosDoblevela']);
Route::get('/getImagesDoblevela', [DobleVelaController::class, 'getImagesDoblevela']);

// StockSur
Route::get('/getProductsStockSur', [StockSurController::class, 'getAllProductsStockSur']);
Route::get('/cleanProductsStockSur', [StockSurController::class, 'cleanProductsStockSur']);

// G4
Route::get('/getProductsG4PL', [G4Controller::class, 'getProductsPL']);
Route::get('/getAllStockG4PL', [G4Controller::class, 'getAllStockPL']);

// Route::get('/getProductsG4BH', [G4Controller::class, 'getProductsBH']);
// Route::get('/getAllStockG4BH', [G4Controller::class, 'getAllStockBH']);

// ImpressLine
Route::get('/getProductsIL', [ImpressLineController::class, 'getProductsIL']);

// EuroCotton
Route::get('/getAllProductsEuroCotton', [EuroCottonController::class, 'getAllProductsEuroCotton']);

// Intuicion Publicitaria
Route::get('/getProductsIP', [IntuicionPublicitariaController::class, 'getProductsIP']);


// Helpers
Route::get('/changeProviderToInternalProducts', [HelperController::class, 'changeProviderToInternalProducts'])->name('companies');


Route::get('/resetProducts', [ResetProducts::class, 'resetProducts']);
