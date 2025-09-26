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
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Master\VendorController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function () {
    // Add your authenticated routes here
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    //Masters

    //Branch Master
    Route::resource('branch', BranchController::class);
    Route::post('get-branches', [BranchController::class, 'getBranches'])->name('get-branches');
    Route::get('branch-excel-export', [BranchController::class, 'branchExcelExport'])->name('branch-excel-export');

    //Location Master
    Route::resource('location', LocationController::class);
    Route::post('get-locations', [LocationController::class, 'getLocations'])->name('get-locations');
    Route::get('location-excel-export', [LocationController::class, 'locationExcelExport'])->name('location-excel-export');

    //Bin Master
    Route::resource('bin', BinController::class);
    Route::post('get-bins', [BinController::class, 'getBins'])->name('get-bins');
    Route::get('bin-excel-export', [BinController::class, 'binExcelExport'])->name('bin-excel-export');

    //Item Master
    Route::resource('item', ItemController::class);
    Route::post('get-item', [ItemController::class, 'getBins'])->name('get-items');

    //Brand Master
    Route::resource('brand', BrandController::class);
    Route::post('get-brands', [BrandController::class, 'getBrands'])->name('get-brands');
    Route::get('brand-excel-export', [BrandController::class, 'brandExcelExport'])->name('brand-excel-export');

    //Reason Master
    Route::resource('reason', ReasonController::class);
    Route::post('get-reasons', [ReasonController::class, 'getReasons'])->name('get-reasons');
    Route::get('reason-excel-export', [ReasonController::class, 'reasonExcelExport'])->name('reason-excel-export');

    //Category Master
    Route::resource('category', CategoryController::class);
    Route::post('get-categories', [CategoryController::class, 'getCategories'])->name('get-categories');
    Route::get('category-excel-export', [CategoryController::class, 'categoryExcelExport'])->name('category-excel-export');

    //subcategory Master
    Route::resource('sub-category', SubCategoryController::class);
    Route::post('get-sub-categories', [SubCategoryController::class, 'getSubCategory'])->name('get-sub-categories');
    Route::get('sub-category-excel-export', [SubCategoryController::class, 'subCategoryExcelExport'])->name('sub-category-excel-export');

    //Uom  Mater
    Route::resource('uom',UomController::class);
    Route::post('get-uoms', [UomController::class, 'getUoms'])->name('get-uoms');
    Route::get('uom-excel-export', [UomController::class, 'uomExcelExport'])->name('uom-excel-export');

    //Customer Master
    Route::resource('customer', CustomerController::class);
    Route::post('get-customers', [CustomerController::class, 'getCustomers'])->name('get-customers');
    Route::get('customer-excel-export', [CustomerController::class, 'customerExcelExport'])->name('customer-excel-export');

    //User Master
    Route::resource('user', UserController::class);
    Route::post('get-users', [UserController::class, 'getUsers'])->name('get-users');
    Route::get('user-excel-export', [UserController::class, 'userExcelExport'])->name('user-excel-export');

    //Vendor Master
    Route::resource('vendr', VendorController::class);
    Route::post('get-vendors', [VendorController::class, 'getVendors'])->name('get-vendors');
    Route::get('vendor-excel-export', [VendorController::class, 'vendorExcelExport'])->name('vendor-excel-export');

});

Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('loging-in', [LoginController::class, 'store'])->name('login.store');
