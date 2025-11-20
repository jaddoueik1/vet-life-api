<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Users\Http\Controllers\AuthController;
use App\Domain\Users\Http\Controllers\UserController;
use App\Domain\Appointments\Http\Controllers\AppointmentController;
use App\Domain\Patients\Http\Controllers\OwnerController;
use App\Domain\Patients\Http\Controllers\PatientController;
use App\Domain\Visits\Http\Controllers\VisitController;
use App\Domain\Invoicing\Http\Controllers\InvoiceController;
use App\Domain\Inventory\Http\Controllers\InventoryController;
use App\Http\Controllers\ConfigController;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/config', [ConfigController::class, 'show']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::apiResource('users', UserController::class);
    Route::middleware('feature:appointments')->apiResource('appointments', AppointmentController::class);
    Route::middleware('feature:patients')->apiResource('owners', OwnerController::class);
    Route::middleware('feature:patients')->apiResource('patients', PatientController::class);
    Route::middleware('feature:visits')->apiResource('visits', VisitController::class);
    Route::middleware('feature:invoicing')->apiResource('invoices', InvoiceController::class);
    Route::middleware('feature:inventory')->apiResource('inventory', InventoryController::class);
});
