<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\BinController;
use App\Http\Controllers\Master\BranchController;
use App\Http\Controllers\Master\BrandController;
use App\Http\Controllers\Master\CategoryController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\ItemController;
use App\Http\Controllers\Master\LocationController;
use App\Http\Controllers\Master\ReasonController;
use App\Http\Controllers\Master\SubCategoryController;
use App\Http\Controllers\Master\UomController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function () {
    // Add your authenticated routes here
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    //Masters

    //Branch Master
    Route::resource('branch', BranchController::class);
    Route::post('get-branches', [BranchController::class, 'getBranches'])->name('get-branches');

    //Location Master
    Route::resource('location', LocationController::class);
    Route::post('get-locations', [LocationController::class, 'getLocations'])->name('get-locations');

    //Bin Master
    Route::resource('bin', BinController::class);
    Route::post('get-bins', [BinController::class, 'getBins'])->name('get-bins');

    //Item Master
    Route::resource('item', ItemController::class);
    Route::post('get-item', [ItemController::class, 'getBins'])->name('get-items');

    //Brand Master
    Route::resource('brand', BrandController::class);
    Route::post('get-brands', [BrandController::class, 'getBrands'])->name('get-brands');

    //Reason Master
    Route::resource('reason', ReasonController::class);
    Route::post('get-reasons', [ReasonController::class, 'getReasons'])->name('get-reasons');

    //Category Master
    Route::resource('category', CategoryController::class);
    Route::post('get-categories', [CategoryController::class, 'getCategories'])->name('get-categories');

    //subcategory Master
    Route::resource('sub-category', SubCategoryController::class);
    Route::post('get-sub-categories', [SubCategoryController::class, 'getSubCategory'])->name('get-sub-categories');

    //Uom  Mater
    Route::resource('uom',UomController::class);
    Route::post('get-uoms', [UomController::class, 'getUoms'])->name('get-uoms');

    //Customer Master
    Route::resource('customer', CustomerController::class);
    Route::post('get-customers', [CustomerController::class, 'getCustomers'])->name('get-customers');

});

Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('loging-in', [LoginController::class, 'store'])->name('login.store');
