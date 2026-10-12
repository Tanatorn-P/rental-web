<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    // แสดงหน้า Login รวม (ที่มี Tab ให้เลือก)
    public function create(Request $request): View
    {
        // รับค่า role จาก query string เช่น ?role=customer (ค่าเริ่มต้นเป็น customer)
        $role = $request->query('role', 'customer');

        return view('auth.login', compact('role'));
    }

    // จัดการการเข้าสู่ระบบแยกตาม Role ที่ส่งมา
    public function store(Request $request): RedirectResponse
    {
        // 1. ดึง role จาก input ถ้าไม่มี ให้เช็กจาก URL Path (รองรับการทดสอบ Test ที่ยิงตรงผ่าน /staff/login หรือ /admin/login)
        $role = $request->input('role');

        if (! $role) {
            $path = $request->path();
            if (str_contains($path, 'staff')) {
                $role = 'staff';
            } elseif (str_contains($path, 'admin')) {
                $role = 'admin';
            } else {
                $role = 'customer';
            }
        }

        if ($role === 'customer') {
            $credentials = $request->validate([
                'username' => ['required', 'string'],
                'password' => ['required', 'string'],
            ]);

            if (! Auth::guard('customer')->attempt($credentials)) {
                return back()->with('error', 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง')->withInput();
            }

            $request->session()->regenerate();

            return redirect()->route('customer.dashboard');

        } else {
            // สำหรับ Staff และ Admin ใช้ guard('web') ร่วมกัน
            $credentials = $request->validate([
                'fullname' => ['required', 'string'],
                'password' => ['required', 'string'],
            ]);

            if (! Auth::guard('web')->attempt($credentials)) {
                return back()->with('error', 'ชื่อหรือรหัสผ่านไม่ถูกต้อง')->withInput();
            }

            $user = Auth::guard('web')->user();

            if ($user->role !== $role) {
                Auth::guard('web')->logout();

                return back()->with('error', 'บัญชีนี้ไม่มีสิทธิ์เข้าสู่ระบบในฐานะ '.ucfirst($role))->withInput();
            }

            $request->session()->regenerate();

            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('staff.dashboard');
            }
        }
    }

    // จัดการ Logout (รองรับทุก Role)
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
