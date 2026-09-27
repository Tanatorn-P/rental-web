<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CustomerRegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.customer-register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:customers,username'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone' => ['required', 'regex:/^08[0-9]{8}$/'],
            'fullname' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'bank_account' => ['nullable', 'string', 'max:50'],
            'bust' => ['nullable', 'numeric'],
            'shoulder' => ['nullable', 'numeric'],
            'waist' => ['nullable', 'numeric'],
            'hips' => ['nullable', 'numeric'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $customer = Customer::create($validated);
        $customer->update(['customer_id' => 'CUS'.str_pad((string) $customer->id, 4, '0', STR_PAD_LEFT)]);

        return redirect()
            ->route('customer.login')
            ->with('success', 'สมัครสมาชิกสำเร็จ กรุณาเข้าสู่ระบบ');
    }
}
