<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PatientController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware([
    'api',
    'auth:sanctum',
])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::resources([
        'addresses' => AddressController::class,
        'patients' => PatientController::class,
    ]);
});
