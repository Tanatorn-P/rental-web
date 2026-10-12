<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminProductController extends Controller
{
    public function index(): View
    {
        $products = Product::all();

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        // Logic to store the product
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(string $id): View
    {
        $products = Product::findOrFail($id);

        return view('admin.products.edit', compact('products'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        // Logic to update the product
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function updateStock(Request $request, string $id): RedirectResponse
    {
        // Logic to update the stock of the product
        return redirect()->route('admin.products.index')->with('success', 'Product stock updated successfully.');
    }

    public function destroy(string $id): RedirectResponse
    {
        // Logic to delete the product
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
