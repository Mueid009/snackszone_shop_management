@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Invoice {{ $order->invoice_no }}</h2>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Date:</strong> {{ $order->created_at->format('F j, Y, h:i A') }}</p>
            <p><strong>Customer:</strong> {{ $order->customer_name ?? 'Guest' }}</p>
            <p><strong>Payment Method:</strong> {{ $order->payment_method ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th style="width:90px">Qty</th>
                        <th style="width:140px">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $it)
                        <tr>
                            <td>{{ $it->product_name }}</td>
                            <td>{{ $it->quantity }}</td>
                            <td>{{ number_format($it->subtotal,2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @php
                $subtotal = $order->items->sum(fn($i) => $i->subtotal);
                $discount = $order->discount ?? 0;
            @endphp

            <div class="mt-3">
                <p>Subtotal: {{ number_format($subtotal,2) }}</p>
                <p>Discount: {{ number_format($discount,2) }}</p>
                <p><strong>Total: {{ number_format($order->total,2) }} ({{ number_format($order->paid ?? 0,2) }})</strong></p>
            </div>

            <a href="{{ route('invoices.print', $order->id) }}" target="_blank" class="btn btn-primary">Print</a>
            <a href="{{ route('invoices.edit', $order->id) }}" class="btn btn-secondary">Edit</a>
        </div>
    </div>
</div>
@endsection
