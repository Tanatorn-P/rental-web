<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerHomeController;
use App\Http\Controllers\CustomerReservationController;
use App\Http\Controllers\CustomerRentalController;
use App\Http\Controllers\CustomerHistoryController;
use App\Http\Controllers\CustomerNotificationController;
use App\Http\Controllers\CustomerProfileController;

Route::middleware(['web'])->prefix('customer')->group(function () {
    // 1. Dashboard / Home (เพิ่ม name customer.dashboard ให้เรียกใช้ได้ทั้งคู่)
    Route::get('/dashboard', [CustomerHomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [CustomerHomeController::class, 'index'])->name('customer.dashboard');

    // 2. My Reservations
    Route::get('/my-reservations', [CustomerReservationController::class, 'index'])->name('reservations.index');

    // 3. My Rentals (Rental Tracking & Timeline)
    Route::get('/my-rentals/{id?}', [CustomerRentalController::class, 'show'])->name('rentals.show');

    // 4. History
    Route::get('/history', [CustomerHistoryController::class, 'index'])->name('history.index');

    // 5. Notifications
    Route::get('/notifications', [CustomerNotificationController::class, 'index'])->name('notifications.index');

    // 6. Profile & Logout
    Route::get('/profile', [CustomerProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [CustomerProfileController::class, 'update'])->name('profile.update');
    Route::post('/logout', [CustomerProfileController::class, 'logout'])->name('logout');
});