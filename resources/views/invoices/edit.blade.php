@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Invoice {{ $order->invoice_no }}</h2>

    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ route('invoices.edit', $order->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card mb-3">
            <div class="card-header">Customer</div>
            <div class="card-body">
                <div class="form-group">
                    <label>Customer Name</label>
                    <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $order->customer_name) }}">
                </div>
                <div class="form-group">
                    <label>Customer Phone</label>
                    <input type="text" name="customer_phone" class="form-control" value="{{ old('customer_phone', $order->customer_phone) }}">
                </div>
                <div class="form-group">
                    <label>Customer Address</label>
                    <textarea name="customer_address" class="form-control">{{ old('customer_address', $order->customer_address) }}</textarea>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Totals</div>
            <div class="card-body">
                @php $subtotal = $order->items->sum(fn($i) => $i->subtotal); @endphp
                <p>Subtotal: {{ number_format($subtotal,2) }}</p>
                <div class="form-group">
                    <label>Discount (BDT)</label>
                    <input type="number" name="discount" step="0.01" min="0" class="form-control" value="{{ old('discount', $order->discount ?? 0) }}">
                </div>
                <div class="form-group">
                    <label>Payment Method</label>
                    <input type="text" name="payment_method" class="form-control" value="{{ old('payment_method', $order->payment_method) }}">
                </div>
            </div>
        </div>

        <button class="btn btn-success" type="submit">Save</button>
        <a href="{{ route('invoices.show', $order->id) }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
