<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Make sure routes are properly defined
Route::apiResource('products', ProductController::class);
Route::patch('products/{product}/toggle-active', [ProductController::class, 'toggleActive']);