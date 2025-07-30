<?php

use Illuminate\Support\Facades\Route;
use admin\brands\Controllers\BrandManagerController;

Route::name('admin.')->middleware(['web', 'admin.auth'])->group(function () {  
    Route::resource('brands', BrandManagerController::class);
    Route::post('brands/updateStatus', [BrandManagerController::class, 'updateStatus'])->name('brands.updateStatus');
});
