<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:web'])->prefix('admin')->name('admin.')->group(function () {
    // 1. หน้าสรุปรายได้และสถิติ
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // 2. หน้าแสดงรายการสินค้า (พร้อมระบบ Filter)
    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');

    // 3. หน้าเพิ่ม/แก้ไขสินค้า (ใช้เทมเพลตเดียวกัน)
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::get('/products/{id}/edit', [AdminProductController::class, 'edit'])->name('products.edit');

    // 4. ขั้นตอนยืนยันก่อนบันทึกจริง (กรอกฟอร์ม -> ดูหน้ายืนยัน -> ค่อยบันทึก)
    //    route literal (commit/cancel) ต้องมาก่อน route ที่มี {id?} ไม่งั้นจะถูกตีความว่าเป็น id
    Route::post('/products/confirm/commit', [AdminProductController::class, 'commit'])->name('products.confirm.commit');
    Route::post('/products/confirm/cancel', [AdminProductController::class, 'cancelConfirm'])->name('products.confirm.cancel');
    Route::post('/products/confirm/{id?}', [AdminProductController::class, 'confirm'])->name('products.confirm');

    // 5. ลบสินค้า (soft delete ผ่าน Eloquent)
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy'])->name('products.destroy');
});
