<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:web'])->prefix('admin')->name('admin.')->group(function () {
    // 1. หน้าสรุปรายได้และสถิติ
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // 2. หน้าแสดงรายการสินค้า (พร้อมระบบ Filter)
    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');

    // 3. หน้าจัดการสินค้า (เพิ่ม / แก้ไข / ปรับเพิ่ม-ลดสต็อก)
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [AdminProductController::class, 'update'])->name('products.update');
    Route::post('/products/{id}/stock', [AdminProductController::class, 'updateStock'])->name('products.update-stock');
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy'])->name('products.destroy');
});