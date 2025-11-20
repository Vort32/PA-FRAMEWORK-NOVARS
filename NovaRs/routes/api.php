<?php

use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\OperationReportController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('admin/equipments/import', [EquipmentController::class, 'import']);
    Route::post('admin/patients/import', [PatientController::class, 'import']);
    Route::get('admin/operations/export', [OperationController::class, 'export']);
});

Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::patch('rooms/{room}/status', [RoomController::class, 'updateStatus']);
    Route::patch('operations/{operation}/status', [OperationController::class, 'updateStatus']);
});

Route::middleware(['auth', 'role:doctor'])->group(function () {
    Route::post('operations/{operation}/report', [OperationReportController::class, 'store']);
    Route::put('operation-reports/{operationReport}', [OperationReportController::class, 'update']);
});
