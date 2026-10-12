<?php

use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\CustomerLoginController;
use App\Http\Controllers\Auth\CustomerRegisterController;
use App\Http\Controllers\Auth\StaffLoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\StaffHistoryController;
use App\Http\Controllers\StaffInspectionController;
use App\Http\Controllers\StaffMaintenanceController;
use App\Http\Controllers\StaffPickupController;
use App\Http\Controllers\StaffQueueController;
use App\Http\Controllers\StaffReturnController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::guard('web')->check()) {
        Auth::guard('web')->logout();
    }

    if (Auth::guard('customer')->check()) {
        Auth::guard('customer')->logout();
    }

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return view('auth.role-select');
})->name('home');

// ---------- Dress catalog & booking (ต้องล็อกอินลูกค้า, guard: customer) ----------
Route::middleware('auth:customer')->group(function () {
    Route::get('/dress/find', [ProductController::class, 'find'])
        ->name('dress.find');

    Route::get('/dress/category/{category}', [ProductController::class, 'category'])
        ->name('dress.category');

    Route::get('/dress/product/{product_id}', [ProductController::class, 'show'])
        ->name('dress.product');

    Route::get('/dress/product/{product_id}/availability',
        [ProductController::class, 'availability'])
        ->name('dress.availability');
    Route::get(
        '/dress/product/{product_id}/booking',
        [ProductController::class, 'bookingSummary']
    )->name('dress.booking.summary'); // ดำเนินการจอง
    Route::get(
        '/dress/product/{product_id}/booking-information',
        [ProductController::class, 'bookingInformation']
    )->name('dress.booking.information');
    Route::post(
        '/dress/product/{product_id}/booking',
        [ProductController::class, 'confirmBooking']
    )->name('dress.booking.confirm'); // กด ยืนยันการจอง เพื่อบันทึกลง orders
    Route::get('/booking/success', function () {
        return view('dress.booking-success');
    })->name('dress.booking.success');
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

    Route::get('/staff/history', [StaffHistoryController::class, 'index'])->name('staff.history.index');
});

// ---------- Auth: Customer (guard: customer) ----------
Route::middleware('guest:customer')->group(function () {
    Route::get('/customer/login', [CustomerLoginController::class, 'create'])->name('customer.login');
    Route::post('/customer/login', [CustomerLoginController::class, 'store']);

    Route::get('/customer/register', [CustomerRegisterController::class, 'create'])->name('customer.register');
    Route::post('/customer/register', [CustomerRegisterController::class, 'store']);
});

Route::middleware('auth:customer')->group(function () {
    Route::post('/customer/logout', [CustomerLoginController::class, 'destroy'])->name('customer.logout');
});

// ดึง Route ฝั่ง Customer จาก user_panel.php เข้ามารวม
require __DIR__.'/user_panel.php';

// ดึง Route ฝั่ง Admin จาก admin_route.php เข้ามารวม
require __DIR__.'/admin_route.php';