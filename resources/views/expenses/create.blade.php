@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4 class="fs-1">Add New Expense</h4>

    <div class="card mt-3">
        <div class="card-body">

            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Expense Title</label>
                    <input type="text" name="title" class="form-control"
                           placeholder="Enter title" required>
                </div>

                <div>
                    <label class="form-label">Expense Quantity (kg)</label>
                    <input type="number" name="quantity" class="form-control"
                           placeholder="Enter quantity" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Amount (৳)</label>
                    <input type="number" name="amount" class="form-control"
                           placeholder="Enter amount" required>
                </div>
                
                <div>
                    <label class="form-label">Expense Description</label>
                    <textarea name="description" class="form-control"
                              placeholder="Enter description" rows="3" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Save Expense</button>
                <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Back</a>
            </form>

        </div>
    </div>
</div>
@endsection
