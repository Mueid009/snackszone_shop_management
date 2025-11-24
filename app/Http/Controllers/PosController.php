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
        'discount'=>'nullable|numeric|min:0',
        'items'=>'required|array|min:1',
        'items.*.product_id'=>'nullable|integer',
        'items.*.product_name'=>'nullable|string',
        'items.*.quantity'=>'nullable|integer|min:1',
        'items.*.unit_price'=>'nullable|numeric|min:0',
    ]);

    // normalize items: skip invalid rows
    $cleanItems = [];
    foreach ($data['items'] as $it) {
        $qty = isset($it['quantity']) ? (int)$it['quantity'] : 0;
        $price = isset($it['unit_price']) ? (float)$it['unit_price'] : 0.0;
        $pname = trim($it['product_name'] ?? '');

        if ($qty < 1) continue;
        if ($pname === '') continue;

        $cleanItems[] = [
            'product_id' => $it['product_id'] ?? null,
            'product_name' => $pname,
            'quantity' => $qty,
            'unit_price' => $price,
        ];
    }

    if (count($cleanItems) === 0) {
        return back()->withInput()->withErrors(['items' => 'At least one product with quantity >= 1 is required.']);
    }

    try {
        DB::beginTransaction();

        // calculate subtotal
        $subtotal = 0;
        foreach ($cleanItems as $it) {
            $subtotal += $it['quantity'] * $it['unit_price'];
        }

        $discount = isset($data['discount']) ? (float)$data['discount'] : 0.0;
        if ($discount < 0) $discount = 0;
        if ($discount > $subtotal) $discount = $subtotal; // clamp

        $finalTotal = round(max(0, $subtotal - $discount), 2);

        $invoice_no = 'INV-'.str_pad((Order::max('id') ?? 0) + 1, 7, '0', STR_PAD_LEFT);

        $orderData = [
            'invoice_no' => $invoice_no,
            'customer_name' => $data['customer_name'] ?? 'Guest',
            'customer_phone' => $data['customer_phone'] ?? null,
            'customer_address' => $data['customer_address'] ?? null,
            'order_description' => $data['order_description'] ?? null,
            'total' => $finalTotal,
            'paid' => 0,
            'payment_method' => $data['payment_method'] ?? null,
            'discount' => $discount, // ensure orders table has this column (migration added)
        ];

        $order = Order::create($orderData);

        // process each item: check stock (if product_id present), decrement, create order item
        foreach ($cleanItems as $it) {
            $productId = $it['product_id'];
            $quantity = (int)$it['quantity'];
            $unitPrice = (float)$it['unit_price'];
            $pname = $it['product_name'];

            if ($productId) {
                // lock the product row for update to prevent race conditions
                $product = Product::lockForUpdate()->find($productId);

                if (!$product) {
                    // product missing in DB: rollback and error
                    throw new \Exception("Product (ID: {$productId}) not found.");
                }

                // check stock
                $currentStock = (int) $product->stock;
                if ($currentStock < $quantity) {
                    throw new \Exception("Not enough stock for product '{$product->product_name}' (available: {$currentStock}, requested: {$quantity}).");
                }

                // decrement stock
                $product->stock = $currentStock - $quantity;
                $product->save();
            }

            // create order item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId ?? null,
                'product_name' => $pname,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $quantity * $unitPrice,
            ]);
        }

        DB::commit();

        return redirect()->route('invoices.index')->with('success','Order created.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('POS store error: '.$e->getMessage());

        // Friendly message to user (Bangla)
        $msg = 'অর্ডার তৈরি করা গেল না: ' . $e->getMessage();
        return back()->withInput()->withErrors(['error' => $msg]);
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

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $order = Order::with('items')->findOrFail($id);

            // Restore stock for products that have product_id
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    // lock product row before updating stock
                    $product = Product::lockForUpdate()->find($item->product_id);
                    if ($product) {
                        $product->stock = (int)$product->stock + (int)$item->quantity;
                        $product->save();
                    }
                }
            }

            // delete order will cascade delete items if foreign key is cascadeOnDelete
            $order->delete();

            DB::commit();
            return redirect()->route('invoices.index')->with('success', 'Order deleted and stock restored (if applicable).');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Order destroy error: '.$e->getMessage());
            return back()->withErrors(['error' => 'Could not delete order: '.$e->getMessage()]);
        }
    }

    public function printInvoice($id)
    {
        $order = Order::with('items')->findOrFail($id);
        return view('invoices.print', compact('order'));
    }
}
