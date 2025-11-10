<?php

use App\Http\Controllers\Ajax\BarcodeReprintAjaxController;
use App\Http\Controllers\Ajax\CommonAjaxController;
use App\Http\Controllers\Ajax\DispatchAjaxController;
use App\Http\Controllers\Ajax\ProductionBarcodeGenerationAJaxController;
use App\Http\Controllers\Ajax\GrnAjaxController;
use App\Http\Controllers\Ajax\OpeningStockAjaxController;
use App\Http\Controllers\Ajax\ProductionIssueAjaxController;
use App\Http\Controllers\Ajax\ProductionStorageScanAjaxController;
use App\Http\Controllers\Ajax\PurchaseOrderAjaxController;
use App\Http\Controllers\Ajax\ReceiptAjaxController;
use App\Http\Controllers\Ajax\SalesReturnAjaxController;
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
        Route::post('item-in-stock', [CommonAjaxController::class, 'itemInStock'])->name('item-in-stock');
        Route::post('get-locations', [CommonAjaxController::class, 'getLocations'])->name('getlocations');

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
        Route::post('fetch-production-scan-details', [ProductionIssueAjaxController::class, 'fetchProductionScanDetails'])->name('fetch-production-scan-details');
        Route::post('production-issue-scan', [ProductionIssueAjaxController::class, 'productionIssueScan'])->name('productionissuescan');

        // Production Barcode Generation Ajax Functions
        Route::post('get-plan-details', [ProductionBarcodeGenerationAJaxController::class, 'getPlanDetails'])->name('getplandetails');

        // Production Storage Scan Ajax Functions
        Route::post('production-storage-scan', [ProductionStorageScanAjaxController::class, 'productionStorageScan'])->name('productionstoragescan');
        Route::post('fetch-fg-storage-scan-details', [ProductionStorageScanAjaxController::class, 'fetchFgStorageScanDetails'])->name('fetchfgstoragescandetails');

        // Dispatch Scan Ajax Functions
        Route::post('get-dispatch-details', [DispatchAjaxController::class, 'getDispatchDetails'])->name('get-dispatch-details');
        Route::post('fetch-dispatch-details', [DispatchAjaxController::class, 'fetchDispatchDetails'])->name('fetch-dispatch-details');
        Route::post('dispatch-scan', [DispatchAjaxController::class, 'dispatchScan'])->name('dispatchscan');

        // Receipt Scan Ajax Functions
        Route::post('receipt-scan', [ReceiptAjaxController::class, 'receiptScan'])->name('receipt-scan');
        Route::post('fetch-receipt-scan-details', [ReceiptAjaxController::class, 'fetchReceiptScanDetails'])->name('fetch-receipt-scan-details');

        // Receipt Scan Ajax Functions
        Route::post('get-opening-stock-items', [OpeningStockAjaxController::class, 'getItems'])->name('get-opening-stock-items');

        // Barcode Reprint Ajax Functions
        Route::post('get-reprint-numbers', [BarcodeReprintAjaxController::class, 'getReprintNumbers'])->name('get-reprint-numbers');

        // Sales Return Ajax Functions
        Route::post('with-barcode-data', [SalesReturnAjaxController::class, 'withBarcodeData'])->name('with-barcode-data');
        Route::post('item-return', [SalesReturnAjaxController::class, 'itemReturn'])->name('item-return');
        Route::post('fetch-bins', [SalesReturnAjaxController::class, 'fetchBins'])->name('fetch-bins');

    });
