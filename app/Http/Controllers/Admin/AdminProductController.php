<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class AdminProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        // Logic to store the product
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {
        $products = Product::findOrFail($id);
        return view('admin.products.edit', compact('products'));
    }

    public function update(Request $request, $id)
    {
        // Logic to update the product
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function updateStock(Request $request, $id)
    {
        // Logic to update the stock of the product
        return redirect()->route('admin.products.index')->with('success', 'Product stock updated successfully.');
    }

    public function destroy($id)
    {
        // Logic to delete the product
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
