<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminOperationRequestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiseaseController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DoctorOperationRequestController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\OperationReportController;
use App\Http\Controllers\OperationRequestReferralLetterController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientOperationRequestController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.attempt');

    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register'])->name('register.store');
});

Route::match(['post', 'get'], 'logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->get('/operation-requests/{operationRequest}/referral-letter', OperationRequestReferralLetterController::class)
    ->name('operation-requests.referral-letter');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('patients/export', [PatientController::class, 'export'])->name('patients.export');
        Route::resource('patients', PatientController::class)->except(['show']);
        Route::post('patients/import', [PatientController::class, 'import'])->name('patients.import');

        Route::resource('doctors', DoctorController::class)->except(['show']);
        Route::resource('staff', StaffController::class)->except(['show']);
        Route::resource('rooms', RoomController::class)->except(['show']);
        Route::resource('equipments', EquipmentController::class)->except(['show']);
        Route::resource('diseases', DiseaseController::class)->except(['show']);
        Route::post('operations/{operation}/approve-request', [OperationController::class, 'approveRequest'])->name('operations.approve-request');
        Route::post('operations/{operation}/reject-request', [OperationController::class, 'rejectRequest'])->name('operations.reject-request');
        Route::resource('operations', OperationController::class)->except(['show']);

        Route::get('operation-requests', [AdminOperationRequestController::class, 'index'])->name('operation-requests.index');
        Route::get('operation-requests/{operationRequest}', [AdminOperationRequestController::class, 'show'])->name('operation-requests.show');
        Route::post('operation-requests/{operationRequest}/approve', [AdminOperationRequestController::class, 'approve'])->name('operation-requests.approve');
        Route::post('operation-requests/{operationRequest}/reject', [AdminOperationRequestController::class, 'reject'])->name('operation-requests.reject');

        Route::post('import/equipments', [EquipmentController::class, 'import'])->name('equipments.import');
        Route::get('export/operations', [OperationController::class, 'export'])->name('operations.export');
    });
});

Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::get('/operations', [OperationController::class, 'index'])->name('operations.index');
    Route::patch('/operations/{operation}/status', [OperationController::class, 'updateStatus'])->name('operations.status');
});

Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/staff/dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');
    Route::patch('/rooms/{room}/status', [RoomController::class, 'updateStatus'])->name('rooms.status');
});

Route::middleware(['auth', 'role:doctor'])->group(function () {
    Route::get('/doctor/dashboard', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');
    Route::get('/doctor/operations', [DoctorController::class, 'operations'])->name('doctor.operations');
    Route::get('/doctor/operations/{operation}/request', [DoctorController::class, 'showRequestForm'])->name('doctor.operations.request');
    Route::post('/doctor/operations/{operation}/request', [DoctorController::class, 'submitRequest'])->name('doctor.operations.request.submit');
    Route::get('/doctor/reports', [DoctorController::class, 'reports'])->name('doctor.reports');

    Route::get('/doctor/operation-requests', [DoctorOperationRequestController::class, 'index'])->name('doctor.operation-requests.index');
    Route::get('/doctor/operation-requests/{operationRequest}', [DoctorOperationRequestController::class, 'show'])->name('doctor.operation-requests.show');
    Route::post('/doctor/operation-requests/{operationRequest}/approve', [DoctorOperationRequestController::class, 'approve'])->name('doctor.operation-requests.approve');
    Route::post('/doctor/operation-requests/{operationRequest}/reject', [DoctorOperationRequestController::class, 'reject'])->name('doctor.operation-requests.reject');

    Route::get('/operations/{operation}/report', [OperationReportController::class, 'create'])->name('doctor.reports.create');
    Route::post('/operations/{operation}/report', [OperationReportController::class, 'store'])->name('doctor.reports.store');
    Route::put('/operation-reports/{operationReport}', [OperationReportController::class, 'update'])->name('doctor.reports.update');
});

Route::middleware(['auth', 'role:patient'])->group(function () {
    Route::get('/patient/dashboard', [PatientController::class, 'dashboard'])->name('patient.dashboard');
    Route::get('/patient/operations', [PatientController::class, 'operations'])->name('patient.operations');
    Route::get('/patient/reports/{operation}', [PatientController::class, 'report'])->name('patient.reports.show');
    Route::get('/patient/operation-requests/create', [PatientOperationRequestController::class, 'create'])->name('patient.operation-requests.create');
    Route::post('/patient/operation-requests', [PatientOperationRequestController::class, 'store'])->name('patient.operation-requests.store');
});
