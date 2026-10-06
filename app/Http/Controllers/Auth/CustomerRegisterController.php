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
            'phone' => ['required', 'regex:/^0[689][0-9]{8}$/'],
            'fullname' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'bank_account' => ['nullable', 'string', 'max:50'],
            'bust' => ['nullable', 'numeric'],
            'shoulder' => ['nullable', 'numeric'],
            'waist' => ['nullable', 'numeric'],
            'hips' => ['nullable', 'numeric'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['customer_id'] = $this->generateCustomerId();

        Customer::create($validated);

        return redirect()
            ->route('customer.login')
            ->with('success', 'สมัครสมาชิกสำเร็จ กรุณาเข้าสู่ระบบ');
    }

    private function generateCustomerId(): string
    {
        $lastId = Customer::query()->orderByDesc('customer_id')->value('customer_id');
        $nextNumber = $lastId ? ((int) substr($lastId, 1)) + 1 : 1;

        return 'C'.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
