<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::post('/products', [ProductController::class, 'store'])
    ->name('products.store');

Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');