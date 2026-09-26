<?php

use App\Http\Controllers\Auth\StaffLoginController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\CustomerLoginController;
use App\Http\Controllers\Auth\CustomerRegisterController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\StaffQueueController;
use App\Http\Controllers\StaffPickupController;
use App\Http\Controllers\StaffReturnController;
use App\Http\Controllers\StaffInspectionController;
use App\Http\Controllers\StaffMaintenanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('customer.login');
});

// ---------- Auth: Staff/Admin (guard: web) ----------
Route::middleware('guest:web')->group(function () {
    Route::get('/staff/login', [StaffLoginController::class, 'create'])->name('staff.login');
    Route::post('/staff/login', [StaffLoginController::class, 'store']);

    Route::get('/admin/login', [AdminLoginController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'store']);
});

Route::middleware('auth:web')->group(function () {
    Route::post('/staff/logout', [StaffLoginController::class, 'destroy'])->name('staff.logout');
    Route::post('/admin/logout', [AdminLoginController::class, 'destroy'])->name('admin.logout');

    Route::get('/staff/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard');

    Route::get('/staff/queue', [StaffQueueController::class, 'index'])->name('staff.queue.index');
    Route::post('/staff/queue/{order}/approve', [StaffQueueController::class, 'approve'])->name('staff.queue.approve');
    Route::post('/staff/queue/{order}/reject', [StaffQueueController::class, 'reject'])->name('staff.queue.reject');

    Route::get('/staff/pickup', [StaffPickupController::class, 'index'])->name('staff.pickup.index');
    Route::post('/staff/pickup/{order}/confirm', [StaffPickupController::class, 'confirm'])->name('staff.pickup.confirm');

    Route::get('/staff/return', [StaffReturnController::class, 'index'])->name('staff.return.index');
    Route::post('/staff/return/{order}/confirm', [StaffReturnController::class, 'confirm'])->name('staff.return.confirm');

    Route::get('/staff/inspection', [StaffInspectionController::class, 'index'])->name('staff.inspection.index');
    Route::post('/staff/inspection/{order}', [StaffInspectionController::class, 'store'])->name('staff.inspection.store');

    Route::get('/staff/maintenance', [StaffMaintenanceController::class, 'index'])->name('staff.maintenance.index');
    Route::post('/staff/maintenance/{product}/complete', [StaffMaintenanceController::class, 'complete'])->name('staff.maintenance.complete');

    // ชั่วคราว จนกว่าจะสร้างหน้า Admin จริง
    Route::get('/admin/dashboard', function () {
        return 'Admin Dashboard (ยังไม่สร้างหน้าจริง)';
    })->name('admin.dashboard');
});

// ---------- Auth: Customer (guard: customer) ----------
// เปลี่ยน path เป็น /customer/... เพื่อไม่ชนกับ /login, /register ของ starter kit เดิม
Route::middleware('guest:customer')->group(function () {
    Route::get('/customer/login', [CustomerLoginController::class, 'create'])->name('customer.login');
    Route::post('/customer/login', [CustomerLoginController::class, 'store']);

    Route::get('/customer/register', [CustomerRegisterController::class, 'create'])->name('customer.register');
    Route::post('/customer/register', [CustomerRegisterController::class, 'store']);
});

Route::middleware('auth:customer')->group(function () {
    Route::post('/customer/logout', [CustomerLoginController::class, 'destroy'])->name('customer.logout');
});

Route::get('/customer/dashboard', function () {
    return 'Customer Dashboard (ยังไม่สร้างหน้าจริง)';
})->middleware('auth:customer')->name('customer.dashboard');