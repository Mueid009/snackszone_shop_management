<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->invoice_no }}</title>
    <style>
        body{ font-family: "Courier New", Courier, monospace; font-size:12px; }
        .receipt { width: 380px; margin: 0 auto; }
        .center { text-align:center; }
        .logo { width: 120px; display:block; margin:0 auto 10px; filter: grayscale(100%); } /* logo black/white */
        .heading { font-size:18px; font-weight:bold; letter-spacing:2px; }
        .items { width:100%; border-top:1px dotted #000; border-bottom:1px dotted #000; margin-top:10px; }
        .items th, .items td { text-align:left; padding:6px 0; }
        .right { float:right; }
        .total { font-weight:bold; border-top:1px solid #000; margin-top:6px; padding-top:6px; }
        .small { font-size:11px; }
        .line { display:flex; justify-content:space-between; margin-bottom:4px; }
    </style>
</head>
<body onload="window.print()">
<div class="receipt">
    <img src="{{ asset('images/snackszone-logo.png') }}" class="logo" alt="logo">
    <div class="center heading">SNACKS ZONE</div>
    <div class="center small">Made with love - Delicious Food</div>

    <h3 class="center">SALES INVOICE</h3>

    <div><strong>Invoice:</strong> {{ $order->invoice_no }}</div>
    <div><strong>Date:</strong> {{ $order->created_at->format('F j, Y') }} &nbsp; {{ $order->created_at->format('h:i:s A') }}</div>
    <div><strong>Customer:</strong> {{ $order->customer_name ?? 'Guest' }}</div>

    <table class="items">
        <thead>
            <tr>
                <th>ITEM</th>
                <th class="right">QTY</th>
                <th class="right">AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $it)
            <tr>
                <td>{{ $it->product_name }}</td>
                <td class="right">{{ $it->quantity }}</td>
                <td class="right">{{ number_format($it->subtotal,2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        @php
            // compute subtotal if discount is stored; otherwise approximate
            $subtotal = $order->items->sum(fn($i) => $i->subtotal);
            $discount = $order->discount ?? 0;
        @endphp

        <div class="line"><span>Subtotal</span> <span>{{ number_format($subtotal,2) }}</span></div>

        @if($discount > 0)
            <div class="line"><span>Discount</span> <span>-{{ number_format($discount,2) }}</span></div>
        @endif

        <div class="line"><span>Total</span> <span>{{ number_format($order->total,2) }} ({{ number_format($order->paid ?? 0,2) }})</span></div>

        <div class="line"><span>Payment Method</span> <span>{{ $order->payment_method ?? 'N/A' }}</span></div>
    </div>

    <div class="center small" style="margin-top:20px;">
        Copyright © {{ date('Y') }} SNACKS ZONE
    </div>
</div>
</body>
</html>
