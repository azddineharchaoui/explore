<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ItineraireController;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth:sanctum'])->group(function (){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    
    Route::apiResource('itineraires', ItineraireController::class);
    
    // liste a visiter
    Route::post('/itineraires/{itineraire}/add-to-wishlist', [ItineraireController::class, 'addToWishlist']);
    Route::delete('/itineraires/{itineraire}/remove-from-wishlist', [ItineraireController::class, 'removeFromWishlist']);
    Route::get('/wishlist', [ItineraireController::class, 'getWishlist']);
    // stats
    Route::get('/stats/categories', [ItineraireController::class, 'getCategoriesStats']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');