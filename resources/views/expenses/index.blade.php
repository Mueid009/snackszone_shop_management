@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fs-1">All Expenses</h4>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary">Add Expense</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Quantity</th>
                        <th>Amount</th>
                        <th>Describtion</th>
                        <th>Date</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($expenses as $exp)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $exp->title }}</td>
                            <td>{{ $exp->quantity }} kg</td>
                            <td>{{ $exp->amount }} ৳</td>
                            <td>{{ $exp->description}}</td>
                            <td>{{ $exp->created_at->format('d M, Y') }}</td>
                            <td>
                                <a href="{{ route('expenses.edit', $exp->id) }}" class="btn btn-sm btn-info">Edit</a>

                                <form action="{{ route('expenses.destroy', $exp->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($expenses->count() == 0)
                <p class="text-center text-muted">No expenses found.</p>
            @endif
        </div>
    </div>
</div>
@endsection
