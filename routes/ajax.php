<?php

use App\Http\Controllers\Ajax\CommonAjaxController;
use App\Http\Controllers\Ajax\PurchaseOrderAjaxController;
use Illuminate\Support\Facades\Route;

Route::prefix('ajax')
    ->middleware(['web', 'auth'])
    ->name('ajax.')
    ->group(function () {

        //Common Ajax Functions
        Route::post('get-spq-quantity', [CommonAjaxController::class, 'getSpqQuantity'])->name('getspqquantity');

        //PurchaseOrder Ajax Functions
        Route::post('get-purchase-order', [PurchaseOrderAjaxController::class, 'getPurchaseOrder'])->name('getpurchaseorder');

    });
