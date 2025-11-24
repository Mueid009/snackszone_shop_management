<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ExpenseController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Product Routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/edit/{id}', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/update/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/delete/{id}', [ProductController::class, 'destroy'])->name('products.delete');

    // Stock Routes
    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::post('/stock/update/{id}', [StockController::class, 'update'])->name('stock.update');

    //Pos
    Route::get('/pos/create', [PosController::class,'create'])->name('pos.create');
    Route::post('/pos/store', [PosController::class,'store'])->name('pos.store');

    Route::get('/invoices', [PosController::class,'index'])->name('invoices.index');
    Route::get('/invoices/{id}', [PosController::class,'show'])->name('invoices.show');
    Route::get('/invoices/{id}/edit', [PosController::class,'edit'])->name('invoices.edit');
    Route::delete('/invoices/{id}', [PosController::class, 'destroy'])->name('invoices.destroy');
    Route::get('/invoices/{id}/print', [PosController::class,'printInvoice'])->name('invoices.print');

    // Report Routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/filter', [ReportController::class, 'filter'])->name('reports.filter');

    Route::resource('expenses', ExpenseController::class);

    Route::get('/about', function () {
        return view('about');
    })->name('about');

});

