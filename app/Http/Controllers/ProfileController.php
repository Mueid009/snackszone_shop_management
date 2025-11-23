<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('id', 'DESC')->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'price'        => 'required|numeric|min:1',
            'stock'        => 'required|integer|min:0',
        ]);

        Product::create([
            'product_name' => trim($request->product_name),
            'price'        => $request->price,
            'stock'        => $request->stock,
        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product Added Successfully!');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id); // safer
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'price'        => 'required|numeric|min:1',
            'stock'        => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($id);

        $product->update([
            'product_name' => trim($request->product_name),
            'price'        => $request->price,
            'stock'        => $request->stock,
        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product Updated Successfully!');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return back()->with('success', 'Product Deleted Successfully!');
    }
}
