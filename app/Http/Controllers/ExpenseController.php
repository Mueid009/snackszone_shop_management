<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::latest()->get();
        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'quantity' => 'required|integer',
            'amount' => 'required|integer',
            'description' => 'nullable'
        ]);

        Expense::create($request->all());
        return redirect()->route('expenses.index')->with('success', 'Expense Added!');
    }
    public function destroy($id)
    {
        Expense::findOrFail($id)->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense Deleted!');
    }
}

