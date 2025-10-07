<?php

use App\Http\Controllers\Ajax\CommonAjaxController;
use Illuminate\Support\Facades\Route;

Route::prefix('ajax')
    ->middleware(['web', 'auth'])
    ->name('ajax.')
    ->group(function () {

        //Common Ajax Functions
        Route::post('get-spq-quantity', [CommonAjaxController::class, 'getSpqQuantity'])->name('getspqquantity');

    });
