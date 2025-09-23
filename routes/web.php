<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\BranchController;
use App\Http\Controllers\Master\LocationController;
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
    Route::post('get-locations', [LocationController::class, 'getlocations'])->name('get-locations');

});

Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('loging-in', [LoginController::class, 'store'])->name('login.store');
