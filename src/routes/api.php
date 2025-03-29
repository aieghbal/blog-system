<?php

use Illuminate\Support\Facades\Route;
use YourVendor\BlogSystem\Controllers\BlogController;
use YourVendor\BlogSystem\Controllers\CategoryController;
use YourVendor\BlogSystem\Controllers\TagController;

Route::middleware(['api'])->group(function () {
    Route::apiResource('blogs', BlogController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('tags', TagController::class);
});
