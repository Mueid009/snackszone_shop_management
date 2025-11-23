@extends('layouts.app')

@section('content')
<style>
    h3,h1{
        font-family: 'Brush Script MT', cursive;
        text-align: center;
    }
    p,  h2, ul{
        font-family: 'Verdana', sans-serif;
        text-align: center;
        
    }
</style>
<div class="bg-white dark:bg-gray-800 p-6 rounded shadow ">

    <h1 class="text-xl font-semibold mt-4">About Snacks Zone</h1>

    <p class="text-gray-500 dark:text-gray-300 leading-7">
        Snacks Zone is a retail mini-store management system designed to track products,
        manage stock, handle POS billing, generate invoices, and calculate business performance.
    </p>

    <h3 class="text-xl font-semibold mt-4">Company Details</h3>

    <ul class="list-disc ml-6 mt-2 text-gray-500 dark:text-gray-300">
        <li>Business Category: Snacks & Mini Store</li>
        <li>Location: Your shop address</li>
        <li>Owner: Your name</li>
        <li>Started: 2025</li>
    </ul>

    <h3 class="text-xl font-semibold mt-4">System Features</h3>

    <ul class="list-disc ml-6 mt-2 text-gray-500 dark:text-gray-300">
        <li>Inventory & Stock Management</li>
        <li>Point of Sale (POS)</li>
        <li>Invoice Download</li>
        <li>Product Analytics & Reports</li>
        <li>Expense Tracking</li>
    </ul>

</div>
@endsection