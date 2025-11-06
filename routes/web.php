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
use App\Http\Controllers\Reports\GrnReportController;
use App\Http\Controllers\Reports\PurchaseOrderReportController;
use App\Http\Controllers\Transactions\Dispatch\DispatchController;
use App\Http\Controllers\Transactions\Dispatch\DispatchScanController;
use App\Http\Controllers\Transactions\Grn\GrnController;
use App\Http\Controllers\Transactions\Production\ProductionIssueController;
use App\Http\Controllers\Transactions\PurchaseEntry\PurchaseOrderController;
use App\Http\Controllers\Transactions\Grn\StorageScanController;
use App\Http\Controllers\Transactions\Production\ProductionBarcodeGenerationController;
use App\Http\Controllers\Transactions\Production\ProductionStorageScanController;
use App\Http\Controllers\Transactions\Production\ProductionPlanController;
use App\Http\Controllers\Transactions\Receipt\ReceiptScanController;
use App\Http\Controllers\Transactions\Stock\OpeningStockController;
use App\Http\Controllers\Transactions\Stock\StockOutController;
use App\Http\Controllers\Utility\PasswordChangeController;
use App\Http\Controllers\Utility\PermissionController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::group(['middleware' => ['isPermitted']], function () {

        // Masters

        // Branch Master
        Route::resource('branch', BranchController::class);

        //Location Master
        Route::resource('location', LocationController::class);

        // Bin Master
        Route::resource('bin', BinController::class);

        // Item Master
        Route::resource('item', ItemController::class);

        // Brand Master
        Route::resource('brand', BrandController::class);

        // Reason Master
        Route::resource('reason', ReasonController::class);

        // Category Master
        Route::resource('category', CategoryController::class);

        // subcategory Master
        Route::resource('sub-category', SubCategoryController::class);

        // Uom  Mater
        Route::resource('uom',UomController::class);

        // Customer Master
        Route::resource('customer', CustomerController::class);

        // User Master
        Route::resource('user', UserController::class);

        // Vendor Master
        Route::resource('vendr', VendorController::class);

        // Transactions

        // PurchaseOrder
        Route::resource('purchase-order', PurchaseOrderController::class);

        // GRN
        Route::resource('grn', GrnController::class);

        // StorageScan
        Route::resource('storage-scan', StorageScanController::class);

        // Production
        //Production Plan
        Route::resource('production-plan', ProductionPlanController::class);

        // Production Scan
        Route::resource('production-issue', ProductionIssueController::class);

        // Production Barcode Generation
        Route::resource('production-barcode-generation', ProductionBarcodeGenerationController::class);

        // Production Storage Scan
        Route::resource('production-storage-scan', ProductionStorageScanController::class);

        //Dispatch
        Route::resource('dispatch', DispatchController::class);

        //Dispatch Scan
        Route::resource('dispatch-scan', DispatchScanController::class);

        //Receipt
        Route::resource('receipt-scan', ReceiptScanController::class);

        //Stock Management
        //Opening Stock
        Route::resource('opening-stock', OpeningStockController::class);

        //Stock Out
        Route::resource('stock-out', StockOutController::class);

        //Reports
        //Purchase Order Reports
        Route::resource('purchase-order-report', PurchaseOrderReportController::class);

        //GRN Reports
        Route::resource('grn-report', GrnReportController::class);

        // Utitlity
        // Permission
        Route::resource('permission', PermissionController::class);

        // Password Change
        Route::resource('change-password', PasswordChangeController::class);

    });

    // Outside isPermitted

    Route::get('print-barcode', [DashboardController::class, 'printBarcode'])->name('printbarcode');
    // Masters

    // Branch Master
    Route::post('get-branches', [BranchController::class, 'getBranches'])->name('get-branches');
    Route::get('branch-excel-export', [BranchController::class, 'branchExcelExport'])->name('branch-excel-export');
    Route::post('branch-excel-upload', [BranchController::class, 'branchExcelUpload'])->name('branch-excel-upload');

    // Location Master
    Route::post('get-locations', [LocationController::class, 'getLocations'])->name('get-locations');
    Route::get('location-excel-export', [LocationController::class, 'locationExcelExport'])->name('location-excel-export');
    Route::post('location-excel-upload', [LocationController::class, 'locationExcelUpload'])->name('location-excel-upload');

    // Bin Master
    Route::post('get-bins', [BinController::class, 'getBins'])->name('get-bins');
    Route::get('bin-excel-export', [BinController::class, 'binExcelExport'])->name('bin-excel-export');
    Route::post('bin-excel-upload', [BinController::class, 'binExcelUpload'])->name('bin-excel-upload');

    // Item Master
    Route::post('get-items', [ItemController::class, 'getItems'])->name('get-items');
    Route::get('item-excel-export', [ItemController::class, 'itemExcelExport'])->name('item-excel-export');
    Route::post('item-excel-upload', [ItemController::class, 'itemExcelUpload'])->name('item-excel-upload');

    // Brand Master
    Route::post('get-brands', [BrandController::class, 'getBrands'])->name('get-brands');
    Route::get('brand-excel-export', [BrandController::class, 'brandExcelExport'])->name('brand-excel-export');
    Route::post('brand-excel-upload', [BrandController::class, 'brandExcelUpload'])->name('brand-excel-upload');

    // Reason Master
    Route::post('get-reasons', [ReasonController::class, 'getReasons'])->name('get-reasons');
    Route::get('reason-excel-export', [ReasonController::class, 'reasonExcelExport'])->name('reason-excel-export');
    Route::post('reason-excel-upload', [ReasonController::class, 'reasonExcelUpload'])->name('reason-excel-upload');

    // Category Master
    Route::post('get-categories', [CategoryController::class, 'getCategories'])->name('get-categories');
    Route::get('category-excel-export', [CategoryController::class, 'categoryExcelExport'])->name('category-excel-export');
    Route::post('category-excel-upload', [CategoryController::class, 'categoryExcelUpload'])->name('category-excel-upload');

    // subcategory Master
    Route::post('get-sub-categories', [SubCategoryController::class, 'getSubCategory'])->name('get-sub-categories');
    Route::get('sub-category-excel-export', [SubCategoryController::class, 'subCategoryExcelExport'])->name('sub-category-excel-export');
    Route::post('sub-category-excel-upload', [SubCategoryController::class, 'subCategoryExcelUpload'])->name('sub-category-excel-upload');

    // Uom  Mater
    Route::post('get-uoms', [UomController::class, 'getUoms'])->name('get-uoms');
    Route::get('uom-excel-export', [UomController::class, 'uomExcelExport'])->name('uom-excel-export');
    Route::post('uom-excel-upload', [UomController::class, 'uomExcelUpload'])->name('uom-excel-upload');

    // Customer Master
    Route::post('get-customers', [CustomerController::class, 'getCustomers'])->name('get-customers');
    Route::get('customer-excel-export', [CustomerController::class, 'customerExcelExport'])->name('customer-excel-export');
    Route::post('customer-excel-upload', [CustomerController::class, 'customerExcelUpload'])->name('customer-excel-upload');

    // User Master
    Route::post('get-users', [UserController::class, 'getUsers'])->name('get-users');
    Route::get('user-excel-export', [UserController::class, 'userExcelExport'])->name('user-excel-export');
    Route::post('user-excel-upload', [UserController::class, 'userExcelUpload'])->name('user-excel-upload');

    // Vendor Master
    Route::post('get-vendors', [VendorController::class, 'getVendors'])->name('get-vendors');
    Route::get('vendor-excel-export', [VendorController::class, 'vendorExcelExport'])->name('vendor-excel-export');
    Route::post('vendor-excel-upload', [VendorController::class, 'vendorExcelUpload'])->name('vendor-excel-upload');

    // Transactions

    // PurchaseOrder
    Route::post('purchase-order-excel-upload', [PurchaseOrderController::class, 'purchaseOrderExcelUpload'])->name('purchase-order-excel-upload');
    Route::get('purchase-order-cancel', [PurchaseOrderController::class, 'purchaseOrderCancel'])->name('purchase-order-cancel');
    Route::get('purchase-order-cancel', [PurchaseOrderController::class, 'purchaseOrderCancel'])->name('purchase-order-cancel');
    Route::post('cancel-purchase-order', [PurchaseOrderController::class, 'cancelPurchaseOrder'])->name('cancel-purchase-order');

    // Grn
    Route::post('grn-excel-upload', [GrnController::class, 'grnExcelUpload'])->name('grn-excel-upload');

    // Production Plan
    Route::post('production-plan-excel-upload', [ProductionPlanController::class, 'productionPlanExcelUpload'])->name('production-plan-excel-upload');

    // Dispatch
    Route::post('dispatch-excel-upload', [DispatchController::class, 'dispatchExcelUpload'])->name('dispatch-excel-upload');

    // Opening Stock
    Route::post('opening-stock-excel-upload', [OpeningStockController::class, 'excelUpload'])->name('opening-stock-excel-upload');

    //Reports
    //Purchase Order Reports
    Route::post('get-purchase-orders', [PurchaseOrderReportController::class, 'getPurchaseOrders'])->name('get-purchase-orders');
    Route::post('get-purchase-detailed', [PurchaseOrderReportController::class, 'getPurchaseDetailed'])->name('get-purchase-detailed');
    Route::get('purchase-order-report/{id}', [PurchaseOrderReportController::class, 'show']) ->name('purchase-order-report.show');

    //GRN Reports
    //Grn Report
    Route::post('get-grn-summary', [GrnReportController::class, 'getGrnSummary'])->name('get-grn-summary');
    // For Item-wise details
    Route::get('grn-report/itemwise/{id}/{item_id}', [GrnReportController::class, 'itemwise'])
        ->name('grn-report.itemwise');

    // For PO-wise details
    Route::get('grn-report/powise/{id}/{item_id}', [GrnReportController::class, 'poWise'])
        ->name('grn-report.powise');

    Route::post('get-grn-po', [GrnReportController::class, 'getGrnPo'])->name('get-grn-po');
    Route::post('get-grn-itemwise', [GrnReportController::class, 'grnItemWise'])->name('get-grn-itemwise');

    Route::post('get-grn-detailed', [GrnReportController::class, 'grnDetailed'])->name('get-grn-detailed');

    // Utitlity

    // Permission
    Route::post('get-permission-menu', [PermissionController::class, 'getPermissionMenu'])->name('get-permission-menu');

});

Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('loging-in', [LoginController::class, 'store'])->name('login.store');
