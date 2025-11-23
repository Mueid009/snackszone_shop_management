@extends('layouts.app')

@section('content')
<div class="container mt-3">

    <h2 class="fs-1">Edit Product</h2>

    <form action="{{ route('products.update',$product->id) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Product Name</label>
        <input type="text" name="product_name" value="{{ $product->product_name }}" class="form-control" required>

        <label class="mt-2">Price</label>
        <input type="number" name="price" value="{{ $product->price }}" class="form-control" required>

        <label class="mt-2">Stock</label>
        <input type="number" name="stock" value="{{ $product->stock }}" class="form-control" required>

        <button class="btn btn-primary mt-3">Update</button>
    </form>

</div>
@endsection
