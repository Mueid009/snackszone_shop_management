@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fs-1">Invoices</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered bg-white">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($orders as $o)
                    <tr>
                        <td>{{ $o->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $o->invoice_no }}</td>
                        <td>{{ $o->customer_name }}</td>
                        <td>{{ $o->items_count }}</td>
                        <td>{{ number_format($o->total,2) }}</td>
                        <td>
                            <a href="{{ route('invoices.show',$o->id) }}" class="btn btn-sm btn-info">View</a>
                            <a href="{{ route('invoices.edit',$o->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <a href="{{ route('invoices.print',$o->id) }}" target="_blank" class="btn btn-sm btn-dark">Print Invoice</a>
                            <form action="{{ route('invoices.destroy', $o->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            @if($orders->count() == 0)
                <p class="text-center text-muted">No order found.</p>
            @endif

        </div>
    </div>

    {{ $orders->links() }}
</div>
@endsection
