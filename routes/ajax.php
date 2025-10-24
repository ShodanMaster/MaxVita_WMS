<?php

use App\Http\Controllers\Ajax\CommonAjaxController;
use App\Http\Controllers\Ajax\ProductionBarcodeGenerationAJaxController;
use App\Http\Controllers\Ajax\GrnAjaxController;
use App\Http\Controllers\Ajax\ProductionIssueAjaxController;
use App\Http\Controllers\Ajax\ProductionStorageScanAjaxController;
use App\Http\Controllers\Ajax\PurchaseOrderAjaxController;
use App\Http\Controllers\Ajax\StorageScanAjaxController;
use Illuminate\Support\Facades\Route;

Route::prefix('ajax')
    ->middleware(['web', 'auth'])
    ->name('ajax.')
    ->group(function () {

        // Common Ajax Functions
        Route::post('get-spq-quantity', [CommonAjaxController::class, 'getSpqQuantity'])->name('getspqquantity');
        Route::post('get-item-uom', [CommonAjaxController::class, 'getItemUom'])->name('getitemuom');
        Route::post('bin-exists', [CommonAjaxController::class, 'binExists'])->name('bin-exists');

        // PurchaseOrder Ajax Functions
        Route::post('get-purchase-order', [PurchaseOrderAjaxController::class, 'getPurchaseOrder'])->name('getpurchaseorder');

        // Grn Ajax Functions
        Route::post('get-purchase-number', [GrnAjaxController::class, 'getPurchaseNumber'])->name('getpurchasenumber');
        Route::post('get-grn-items', [GrnAjaxController::class, 'getGrnItems'])->name('getgrnitems');
        Route::post('item-purchase-quantity', [GrnAjaxController::class, 'itemPurchaseQuantity'])->name('itempurchasequantity');

        // Storage Scan Ajax Functions
        Route::post('get-grn-details', [StorageScanAjaxController::class, 'getGrnDetails'])->name('getgrndetails');
        Route::post('fetch-storage-scan-details', [StorageScanAjaxController::class, 'fetchStorageScanDetails'])->name('fetch-storage-scan-details');
        Route::post('storage-scan', [StorageScanAjaxController::class, 'storageScan'])->name('storagescan');

        // Production Scan Ajax Functions
        Route::post('get-production-details', [ProductionIssueAjaxController::class, 'getProductionDetails'])->name('getproductiondetails');
        Route::post('production-issue-scan', [ProductionIssueAjaxController::class, 'productionIssueScan'])->name('productionissuescan');

        // Production Barcode Generation Ajax Functions
        Route::post('get-plan-details', [ProductionBarcodeGenerationAJaxController::class, 'getPlanDetails'])->name('getplandetails');

        // Production Storage Scan Ajax Functions
        Route::post('production-storage-scan', [ProductionStorageScanAjaxController::class, 'productionStorageScan'])->name('productionstoragescan');
    });
