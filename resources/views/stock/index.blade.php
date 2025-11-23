@extends('layouts.app')

@section('content')
<div class="container mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fs-1">Stock Management</h4> 
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered mt-3 bg-white">
                <tr>
                    <th>Product</th>
                    <th>Current Stock</th>
                    <th>Update</th>
                </tr>

                @foreach ($products as $product)
                <tr>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->stock }}</td>

                    <td>
                        <form action="{{ route('stock.update',$product->id) }}" method="POST" class="d-flex">
                            @csrf

                            <input type="number" name="qty" class="form-control w-25" min="1" placeholder="Qty" required>

                            <button type="submit" name="type" value="add" class="btn btn-success ms-2">Add</button>
                            <button type="submit" name="type" value="remove" class="btn btn-danger ms-2">Remove</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </table>

            @if($products->count() == 0)
                <p class="text-center text-muted">No stocks found.</p>
            @endif
        </div>
    </div>

</div>
@endsection
