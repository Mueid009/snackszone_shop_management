@extends('layouts.app')

@section('content')
<div class="container mt-3">

    <h2 class="fs-1">Add Product</h2>

    <form action="{{ route('products.store') }}" method="POST">
        @csrf

        <label>Product Name</label>
        <input type="text" name="product_name" class="form-control" required>

        <label class="mt-2">Price</label>
        <input type="number" name="price" class="form-control" required>

        <label class="mt-2">Initial Stock</label>
        <input type="number" name="stock" class="form-control" required>

        <button class="btn btn-success mt-3">Save</button>
    </form>

</div>
@endsection
