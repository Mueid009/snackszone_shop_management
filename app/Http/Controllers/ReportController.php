<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Expense;
use Carbon\Carbon;
use DB;

class ReportController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $report = $this->generateReport($today, $today);

        return view('reports.index', [
            'report' => $report,
            'type'   => 'Today',
        ]);
    }

    public function filter(Request $request)
    {
        $request->validate([
            'filter_type' => 'required'
        ]);

        $type = $request->filter_type;

        switch ($type) {

            case 'today':
                $from = $to = Carbon::today();
                break;

            case 'weekly':
                $from = Carbon::now()->startOfWeek();
                $to   = Carbon::now()->endOfWeek();
                break;

            case 'monthly':
                $from = Carbon::now()->startOfMonth();
                $to   = Carbon::now()->endOfMonth();
                break;

            case 'yearly':
                $from = Carbon::now()->startOfYear();
                $to   = Carbon::now()->endOfYear();
                break;

            case 'custom':
                $from = Carbon::parse($request->from_date);
                $to   = Carbon::parse($request->to_date);
                break;

            default:
                abort(404);
        }

        $report = $this->generateReport($from, $to);

        return view('reports.index', [
            'report' => $report,
            'type'   => ucfirst($type),
            'from'   => $from,
            'to'     => $to,
        ]);
    }


    // MAIN FUNCTION: GENERATE REPORT DATA
    private function generateReport($from, $to)
{
        // Overall revenue and expense
        $revenue = Order::whereBetween('created_at', [$from, $to])->sum('total');
        $expense = Expense::whereBetween('created_at', [$from, $to])->sum('amount');

        // Daily Sales Trend (Line Chart)
        $dailySales = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total) as total')
        )
        ->whereBetween('created_at', [$from, $to])
        ->groupBy('date')
        ->orderBy('date', 'ASC')
        ->get()
        ->map(function ($row) {
            return [
                'date' => $row->date,
                'total' => (int) $row->total,
            ];
        });

        // Product-wise Sales: aggregate from JSON 'items' column in PHP
        // (Order model already casts 'items' => 'array')
        $orders = Order::whereBetween('created_at', [$from, $to])->get();
        $productTotals = [];

        foreach ($orders as $order) {
            $items = $order->items ?? [];
            // items should be an array of product lines: [{product_id: x, quantity: n, ...}, ...]
            foreach ($items as $it) {
                // tolerate different key names (product_id or id) and quantity keys
                $productId = $it['product_id'] ?? $it['id'] ?? null;
                $qty = $it['quantity'] ?? $it['qty'] ?? $it['qty'] ?? 1;

                if (!$productId) {
                    continue;
                }

                // coerce to int
                $qty = (int) $qty;

                if (!isset($productTotals[$productId])) {
                    $productTotals[$productId] = 0;
                }

                $productTotals[$productId] += $qty;
            }
        }

        $productSales = collect($productTotals)->map(function ($qty, $productId) {
            return [
                'product_id' => $productId,
                'total_qty'  => $qty,
            ];
        })->values();

        // Profit Growth (daily profit)
        $dailyProfit = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total) as revenue')
        )
        ->whereBetween('created_at', [$from, $to])
        ->groupBy('date')
        ->orderBy('date', 'ASC')
        ->get()
        ->map(function ($item) {
            return (object)[
                'date' => $item->date,
                'revenue' => (int) $item->revenue,
            ];
        })
        ->map(function ($item) {
            // compute expense for that date
            $expenseForDate = Expense::whereDate('created_at', $item->date)->sum('amount');
            return [
                'date' => $item->date,
                'profit' => ((int)$item->revenue - (int)$expenseForDate),
            ];
        });

        // Prepare charts payload (blade expects $report['charts'][...])
        $charts = [
            'bar' => ['revenue' => (int)$revenue, 'expense' => (int)$expense],
            'daily' => $dailySales,        // array of {date, total}
            'products' => $productSales,   // array of {product_id, total_qty}
            'profit' => $dailyProfit,      // daily profit growth
        ];

        return [
            'from'      => $from->format('d M Y'),
            'to'        => $to->format('d M Y'),
            'revenue'   => (int)$revenue,
            'expense'   => (int)$expense,
            'profit'    => (int)$revenue - (int)$expense, // overall profit
            'charts'    => $charts,
            // keep product list accessible directly if needed
            'products'  => $productSales,
            // if you want daily profit separately too:
            'profit_growth' => $dailyProfit,
        ];
    }
}