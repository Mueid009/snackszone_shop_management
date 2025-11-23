<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index() {
        $products = Product::all();
        return view('stock.index', compact('products'));
    }

    public function update(Request $request, $id) {

        // VALIDATION
        $request->validate([
            'qty'  => 'required|integer|min:1',
            'type' => 'required|in:add,remove',
        ]);

        $product = Product::findOrFail($id);
        $qty = (int) $request->qty;

        // CHECK TYPE EXPLICITLY
        if ($request->type === "add") {

            $product->stock += $qty;

        } elseif ($request->type === "remove") {

            // PREVENT NEGATIVE STOCK
            if ($product->stock < $qty) {
                return back()->with('error', 'Not enough stock to remove!');
            }

            $product->stock -= $qty;
        } else {
            return back()->with('error', 'Invalid action type!');
        }

        $product->save();

        return back()->with('success', 'Stock Updated Successfully!');
    }
}
