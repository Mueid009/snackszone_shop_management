@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4 class="fs-1">Edit Expense</h4>

    <div class="card mt-3">
        <div class="card-body">

            <form action="{{ route('expenses.update', $expense->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Expense Title</label>
                    <input type="text" name="title" value="{{ $expense->title }}"
                           class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Expense Quantity</label>
                    <input type="number" name="quantity" value="{{ $expense->quantity }}"
                           class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Amount (৳)</label>
                    <input type="number" name="amount" value="{{ $expense->amount }}"
                           class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Expense Description</label>
                    <textarea name="description" class="form-control" rows="3"
                              required>{{ $expense->description }}</textarea>
                </div>

                <button type="submit" class="btn btn-success">Update Expense</button>
                <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Back</a>
            </form>

        </div>
    </div>
</div>
@endsection
