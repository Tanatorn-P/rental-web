<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerProfileController extends Controller
{
    public function index(): View
    {
        $customer = Auth::guard('customer')->user() ?? Customer::first();

        return view('customer.profile.index', compact('customer'));
    }

    public function update(Request $request): RedirectResponse
    {
        $customer = Auth::guard('customer')->user() ?? Customer::first();

        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'bank_account' => 'nullable|string|max:100',
            'bust' => 'nullable|numeric',
            'shoulder' => 'nullable|numeric',
            'waist' => 'nullable|numeric',
            'hips' => 'nullable|numeric',
        ]);

        if ($customer) {
            $customer->update($validated);
        }

        return redirect()->route('customer.profile.index')->with('success', 'อัปเดตข้อมูลส่วนตัวเรียบร้อยแล้ว');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
