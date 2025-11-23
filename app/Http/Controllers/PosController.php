<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PosController extends Controller
{
    public function create()
    {
        $products = Product::orderBy('product_name')->get();

        return view('pos.create', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name'=>'nullable|string|max:255',
            'customer_phone'=>'nullable|string|max:50',
            'customer_address'=>'nullable|string',
            'order_description'=>'nullable|string',
            'payment_method'=>'nullable|string',
            'items'=>'required|array|min:1',
            'items.*.product_id'=>'nullable|integer',
            'items.*.product_name'=>'required|string',
            'items.*.quantity'=>'required|integer|min:1',
            'items.*.unit_price'=>'required|numeric|min:0',
        ]);

        // server-side stock checks
        // build a map product_id => required_qty
        $required = [];
        foreach ($data['items'] as $it) {
            if (!empty($it['product_id'])) {
                $pid = (int) $it['product_id'];
                $required[$pid] = ($required[$pid] ?? 0) + (int)$it['quantity'];
            }
        }

        // Begin transaction to check & reserve stock atomically
        DB::beginTransaction();
        try {
            foreach ($required as $pid => $qtyNeeded) {
                // lock row for update
                $product = Product::where('id', $pid)->lockForUpdate()->first();

                if (!$product) {
                    throw ValidationException::withMessages(["items" => "Product (id: $pid) not found."]);
                }

                if ($product->stock < $qtyNeeded) {
                    // rollback and return with error
                    DB::rollBack();
                    return back()->withInput()->withErrors(['stock' => "Not enough stock for {$product->product_name}. Available: {$product->stock}, required: {$qtyNeeded}"]);
                }

                // else we will decrement later after creating order items (or do it now)
                // Option: decrement now to reserve
                $product->stock -= $qtyNeeded;
                $product->save();
            }

            // all checks passed, create order and items
            $total = 0;
            foreach ($data['items'] as $it) {
                $total += ($it['quantity'] * $it['unit_price']);
            }

            $invoice_no = 'INV-'.str_pad((Order::max('id') ?? 0) + 1, 7, '0', STR_PAD_LEFT);

            $order = Order::create([
                'invoice_no'=>$invoice_no,
                'customer_name'=>$data['customer_name'] ?? 'Guest',
                'customer_phone'=>$data['customer_phone'] ?? null,
                'customer_address'=>$data['customer_address'] ?? null,
                'order_description'=>$data['order_description'] ?? null,
                'total'=>$total,
                'paid'=>0,
                'payment_method'=>$data['payment_method'] ?? null,
            ]);

            foreach ($data['items'] as $it) {
                OrderItem::create([
                    'order_id'=>$order->id,
                    'product_id'=>$it['product_id'] ?? null,
                    'product_name'=>$it['product_name'],
                    'quantity'=>$it['quantity'],
                    'unit_price'=>$it['unit_price'],
                    'subtotal'=> $it['quantity'] * $it['unit_price'],
                ]);
            }

            DB::commit();
            return redirect()->route('invoices.index')->with('success','Order created.');
        } catch (\Exception $e) {
            DB::rollBack();
            // log error if you want: \Log::error($e);
            return back()->withInput()->withErrors(['error' => 'Could not create order: '.$e->getMessage()]);
        }
    }

    public function index()
    {
        $orders = Order::withCount('items')->orderBy('created_at','desc')->paginate(15);
        return view('invoices.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('items')->findOrFail($id);
        return view('invoices.show', compact('order'));
    }

    public function edit($id)
    {
        $order = Order::with('items')->findOrFail($id);
        $products = Product::orderBy('product_name')->get();
        return view('invoices.edit', compact('order','products'));
    }

    public function update(Request $request, $id)
    {
        // For brevity: similar to store; update order & items
        // (implementation left as exercise)
        return back()->with('success','Updated (implement similarly to store).');
    }

    public function printInvoice($id)
    {
        $order = Order::with('items')->findOrFail($id);
        return view('invoices.print', compact('order'));
    }
}
