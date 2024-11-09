<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TravelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('travels', TravelController::class);

Route::post('/register', ([AuthController::class, 'register']));
Route::post('/login', ([AuthController::class, 'login']));
Route::post('/logout', ([AuthController::class, 'logout']))->middleware('auth:sanctum');