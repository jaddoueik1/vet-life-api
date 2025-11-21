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
use App\Domain\Medications\Http\Controllers\MedicationController;
use App\Domain\Services\Http\Controllers\ServiceController;
use App\Http\Controllers\ConfigController;
use App\Domain\Staff\Http\Controllers\StaffController;
use App\Domain\Staff\Http\Controllers\VeterinarianController;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/config', [ConfigController::class, 'show']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::apiResource('users', UserController::class);
    Route::apiResource('veterinarians', VeterinarianController::class);
    Route::apiResource('staff', StaffController::class);
    Route::middleware('feature:appointments')->apiResource('appointments', AppointmentController::class);
    Route::middleware('feature:patients')->apiResource('owners', OwnerController::class);
    Route::middleware('feature:patients')->group(function () {
        Route::get('patients/{patient}/details', [PatientController::class, 'details']);
        Route::apiResource('patients', PatientController::class);
    });
    Route::middleware('feature:visits')->apiResource('visits', VisitController::class);
    Route::middleware('feature:invoicing')->group(function () {
        Route::post('invoices/{invoice}/payment', [InvoiceController::class, 'recordPayment']);
        Route::apiResource('invoices', InvoiceController::class);
    });
    Route::middleware('feature:inventory')->group(function () {
        Route::get('inventory/items/low-stock', [InventoryController::class, 'lowStock']);
        Route::apiResource('inventory', InventoryController::class);
    });
    Route::middleware('feature:medications')->apiResource('medications', MedicationController::class);
    Route::middleware('feature:services')->apiResource('services', ServiceController::class);
});
