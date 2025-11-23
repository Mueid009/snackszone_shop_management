<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();   // ডাটাবেস থেকে সব প্রোডাক্ট

        $cart = session()->get('cart', []); // কার্ট থাকলে নিন, না থাকলে খালি অ্যারে

        return view('products.index', compact('products', 'cart'));
    }

    public function create() {
        return view('products.create');
    }

    public function store(Request $request) {
        $request->validate([
            'product_name' => 'required',
            'price' => 'required',
            'stock' => 'required',
        ]);

        Product::create([
            'product_name' => $request->product_name,
            'price'        => $request->price,
            'stock'        => $request->stock,
        ]);

        return redirect()->route('products.index')->with('success', 'Product Added!');
    }

    public function edit($id) {
        $product = Product::find($id);
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, $id) {
        $product = Product::find($id);

        $product->update([
            'product_name' => $request->product_name,
            'price'        => $request->price,
            'stock'        => $request->stock,
        ]);

        return redirect()->route('products.index')->with('success', 'Product Updated!');
    }

    public function destroy($id) {
        Product::find($id)->delete();
        return back()->with('success', 'Product Deleted!');
    }
}
