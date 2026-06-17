<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::middleware([
    'api',
    'auth:sanctum'
])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::resources([
        'addresses' => AddressController::class
    ]);
});
