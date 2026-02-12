<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// --- Auth Proxy Endpoints ---
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::middleware('auth.supabase')->put('/user', [AuthController::class, 'update']);
});

// Authenticated user endpoints
Route::middleware('auth.supabase')->group(function () {
    Route::get('/user', [UserController::class, 'me']);
    Route::get('/users', [UserController::class, 'index'])->middleware('role:admin');
});

// --- Public Read-Only Endpoints ---
Route::get('/brands', [BrandController::class, 'index']);
Route::get('/brands/{id}', [BrandController::class, 'show']);

Route::get('/products', [ProductController::class, 'index']);
Route::post('/products/search-similar', [ProductController::class, 'searchSimilar']); // New Semantic Search Endpoint
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::get('/countries', [CountryController::class, 'index']);
Route::get('/countries/{id}', [CountryController::class, 'show']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

Route::get('/stores', [StoreController::class, 'index']);
Route::get('/stores/{id}', [StoreController::class, 'show']);

Route::middleware(['auth.supabase', 'role:admin'])->group(function () {

    Route::prefix('brands')->group(function () {
        Route::post('/', [BrandController::class, 'store']);
        Route::put('/{id}', [BrandController::class, 'update']);
    });

    Route::prefix('products')->group(function () {
        Route::post('/', [ProductController::class, 'store']);
        Route::post('/sync', [ProductController::class, 'sync']);
        Route::put('/{id}', [ProductController::class, 'update']);
    });

    Route::prefix('countries')->group(function () {
        Route::post('/', [CountryController::class, 'store']);
        Route::put('/{id}', [CountryController::class, 'update']);
    });

    Route::prefix('categories')->group(function () {
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{id}', [CategoryController::class, 'update']);
    });

    Route::prefix('stores')->group(function () {
        Route::post('/', [StoreController::class, 'store']);
        Route::put('/{id}', [StoreController::class, 'update']);
        Route::post('/{id}/categories', [StoreController::class, 'syncCategories']);
    });
});
