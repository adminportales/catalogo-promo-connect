<?php

use App\Http\Controllers\ConsultSuppliers;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';

// Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//Route Hooks - Do not delete//
Route::view('users', 'livewire.users.index')->middleware('auth');
Route::view('products', 'livewire.products.index')->middleware('auth');
Route::view('subcategories', 'livewire.subcategories.index')->middleware('auth');
Route::view('categories', 'livewire.categories.index')->middleware('auth');
Route::view('providers', 'livewire.providers.index')->middleware('auth');

Route::get('/getAllProductsInnova', [ConsultSuppliers::class, 'getAllProductsInnova']);
Route::get('/getStockInnova', [ConsultSuppliers::class, 'getStockInnova']);

Route::get('/getAllProductsPromoOption', [ConsultSuppliers::class, 'getAllProductsPromoOption']);
Route::get('/getPricePromoOpcion', [ConsultSuppliers::class, 'getPricePromoOpcion']);
Route::get('/getStockPromoOpcion', [ConsultSuppliers::class, 'getStockPromoOpcion']);

Route::get('/getAllProductsForPromotional', [ConsultSuppliers::class, 'getAllProductsForPromotional']);
