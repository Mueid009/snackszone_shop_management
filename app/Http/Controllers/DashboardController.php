<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Expense;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Filter (default: today)
        $filter = $request->get('filter', 'today');

        // Date Range
        switch ($filter) {
            case 'week':
                $start = Carbon::now()->startOfWeek();
                $end   = Carbon::now()->endOfWeek();
                break;

            case 'month':
                $start = Carbon::now()->startOfMonth();
                $end   = Carbon::now()->endOfMonth();
                break;

            case 'year':
                $start = Carbon::now()->startOfYear();
                $end   = Carbon::now()->endOfYear();
                break;

            default: // today
                $start = Carbon::today()->startOfDay();
                $end   = Carbon::today()->endOfDay();
        }

        // ---- Product Analytics ----
        $totalItems   = Product::count();
        $totalInStock = Product::where('stock', '>', 0)->count();
        $lowStock     = Product::where('stock', '<=', 5)->count(); // threshold 5

        $inStockPercent = $totalItems > 0 ? round(($totalInStock / $totalItems) * 100, 1) : 0;

        // ---- Sales Analytics ----
        $totalRevenue = (float) Order::sum('total');

        // filtered revenue by date range
        $filteredRevenue = (float) Order::whereBetween('created_at', [$start, $end])->sum('total');
        $totalOrders     = Order::whereBetween('created_at', [$start, $end])->count();

        // ---- Expenses ----
        $totalExpenses = Expense::sum('amount');
        

        // ---- Monthly revenue chart (12 months) ----
        $monthlyRaw = Order::selectRaw("MONTH(created_at) as month, SUM(total) as total")
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthly = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthly[$m] = isset($monthlyRaw[$m]) ? (float)$monthlyRaw[$m] : 0;
        }

        // ---- Top selling products (by qty) ----
        $topSelling = OrderItem::selectRaw('order_items.product_id, products.product_name, SUM(order_items.quantity) as total_qty')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->groupBy('order_items.product_id', 'products.product_name')
            ->orderByDesc('total_qty')
            ->limit(6)
            ->get();

        // ---- Low stock items list (sample) ----
        $lowStockItems = Product::where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->limit(6)
            ->get();

        // pass simple product list for dynamic cart
        $products = Product::orderBy('product_name')->get(['id','product_name','price','stock']);

        return view('dashboard.index', compact(
            'filter',
            'totalItems',
            'totalInStock',
            'lowStock',
            'inStockPercent',
            'totalRevenue',
            'filteredRevenue',
            'totalOrders',
            'totalExpenses',
            'monthly',
            'topSelling',
            'lowStockItems',
            'products'
        ));
    }
}
