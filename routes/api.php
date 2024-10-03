<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('password/email', [AuthController::class, 'sendResetLinkEmail']);
Route::post('password/reset', [AuthController::class, 'resetPassword']);

Route::prefix('/articles')->group(function () {
    
    /**
     * News Source Routes
     */
    Route::controller(App\Http\Controllers\NewsSourceController::class)
    ->prefix('/news-source')->group(function(){
            Route::get('/','index');
            Route::post('/store','store');
            Route::get('/show/{id}','show');
    });
});


// User Routes

Route::middleware('auth:sanctum')->prefix('/user')->group(function () {
    
    /**
     * News articles Routes
     */
    Route::controller(App\Http\Controllers\User\NewsArticleController::class)
    ->prefix('/news-article')->group(function(){
            Route::get('/','index');
            Route::post('/set','setNewsArticles');
    });
});