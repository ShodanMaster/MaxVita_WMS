<?php

use App\Http\Controllers\Ajax\CommonAjaxController;
use App\Http\Controllers\Ajax\GrnAjaxController;
use App\Http\Controllers\Ajax\PurchaseOrderAjaxController;
use Illuminate\Support\Facades\Route;

Route::prefix('ajax')
    ->middleware(['web', 'auth'])
    ->name('ajax.')
    ->group(function () {

        //Common Ajax Functions
        Route::post('get-spq-quantity', [CommonAjaxController::class, 'getSpqQuantity'])->name('getspqquantity');
        Route::post('get-item-uom', [CommonAjaxController::class, 'getItemUom'])->name('getitemuom');

        //PurchaseOrder Ajax Functions
        Route::post('get-purchase-order', [PurchaseOrderAjaxController::class, 'getPurchaseOrder'])->name('getpurchaseorder');

        //Grn Ajax Functions
        Route::post('get-purchase-number', [GrnAjaxController::class, 'getPurchaseNumber'])->name('getpurchasenumber');
        Route::post('get-grn-items', [GrnAjaxController::class, 'getGrnItems'])->name('getgrnitems');

    });
