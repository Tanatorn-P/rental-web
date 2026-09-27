<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StaffLoginController extends Controller
{
    public function create(): View
    {
        return view('auth.staff-login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'fullname' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->attempt($credentials)) {
            return back()->with('error', 'ชื่อหรือรหัสผ่านไม่ถูกต้อง');
        }

        if (Auth::guard('web')->user()->role !== 'staff') {
            Auth::guard('web')->logout();

            return back()->with('error', 'บัญชีนี้ไม่ใช่บัญชีพนักงาน กรุณาเข้าสู่ระบบที่หน้า Admin');
        }

        $request->session()->regenerate();

        return redirect()->route('staff.dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('staff.login');
    }
}
