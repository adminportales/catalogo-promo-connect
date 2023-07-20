<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BatchInputProducts;
use App\Http\Controllers\ConsultSuppliers;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Providers\DobleVelaController;
use App\Http\Controllers\Providers\ForPromotionalController;
use App\Http\Controllers\Providers\G4Controller;
use App\Http\Controllers\Providers\ImpressLineController;
use App\Http\Controllers\Providers\InnovationController;
use App\Http\Controllers\Providers\IUSBController;
use App\Http\Controllers\Providers\PromoOpcionController;
use App\Http\Controllers\Providers\StockSurController;
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
Route::view('/', 'cotizador.catalogo.index');
