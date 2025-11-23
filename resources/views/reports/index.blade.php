@extends('layouts.app')

@section('content')

<div class="container mt-4">

    <h2 class="fs-1">Shop Reports ({{ $type }})</h2>

    <div class="card p-3 mt-3">
        <form action="{{ route('reports.filter') }}" method="POST">
            @csrf

            <div class="row justify-content-center">
                <div class="col-md-3 center">
                    <label>Report Type</label>
                    <select name="filter_type" class="form-control" id="filter_type">
                        <option value="today">Today</option>
                        <option value="weekly">This Week</option>
                        <option value="monthly">This Month</option>
                        <option value="yearly">This Year</option>
                        <!-- <option value="custom">Custom Range</option> -->
                    </select>
                </div>

                <div class="col-md-3 custom-date ">
                    <label>From</label>
                    <input type="date" name="from_date" class="form-control">
                </div>

                <div class="col-md-3 custom-date ">
                    <label>To</label>
                    <input type="date" name="to_date" class="form-control">
                </div>

                <div class="col-md-3">
                    <button class="btn btn-primary mt-4">Show Report</button>
                </div>
            </div>

        </form>
    </div>

    <div class="card mt-4 p-3">
        <h4>Report Summary ({{ $report['from'] }} → {{ $report['to'] }})</h4>

        <table class="table table-bordered mt-3">
            <tr>
                <th width="40%">Revenue</th>
                <td>{{ number_format($report['revenue']) }} ৳</td>
            </tr>
            <tr>
                <th>Expenses</th>
                <td>{{ number_format($report['expense']) }} ৳</td>
            </tr>
            <tr>
                <th>Profit</th>
                <td><b>{{ number_format($report['profit']) }} ৳</b></td>
            </tr>
        </table>
    </div>

    <div class="card mt-4 p-3">
    <h3 class="mb-3">📊 Visual Reports</h3>

    {{-- Revenue vs Expense Bar Chart --}}
    <h5>Revenue vs Expense</h5>
    <canvas id="reBarChart" height="120"></canvas>

    <hr>

    {{-- Daily Sales Trend Line Chart --}}
    <h5>Daily Sales Trend</h5>
    <canvas id="dailyLineChart" height="120"></canvas>

    <hr>

    {{-- Product-wise Sales Chart --}}
    <h5>Product-wise Sales</h5>
    <canvas id="productChart" height="120"></canvas>

    <hr>

    {{-- Profit Growth Line Chart --}}
    <h5>Profit Growth</h5>
    <canvas id="profitLineChart" height="120"></canvas>
</div>


</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php
    $barData = json_encode($report['charts']['bar']);
    $dailySales = json_encode($report['charts']['daily']);
    $productSales = json_encode($report['charts']['products']);
    $profitData = json_encode($report['charts']['profit']);
?>

<script>
    const width = 500;
    const height = 400;
    const margin = { top: 20, right: 20, bottom: 40, left: 40 };
    const padding= { top: 10, right: 0, bottom: 10, left: 0}

    // Apply width & height to all chart canvases
    document.querySelectorAll("canvas").forEach(c => {
        c.width = width;
        c.height = height;
        c.padding = padding;
        c.style.display = "block";
        c.style.margin = "0 auto"; // center

    });
</script>

<script>
// {{-- SCRIPT: Show/hide custom date fields --}}

// document.getElementById('filter_type').addEventListener('change', function () {
//     // const custom = document.querySelectorAll('.custom-date');

//     // if (this.value === 'custom') {
//     //     custom.forEach(x => x.classList.remove('d-none'));
//     // } else {
//     //     custom.forEach(x => x.classList.add('d-none'));
//     // }
// });
    // Convert PHP to JS
    const barData = <?php echo $barData; ?>;
    const dailySales = <?php echo $dailySales; ?>;
    const productSales = <?php echo $productSales; ?>;
    const profitData = <?php echo $profitData; ?>;

    // ========== 1. Revenue vs Expense BAR CHART ==========
    new Chart(document.getElementById('reBarChart'), {
        type: 'bar',
        data: {
            labels: ['Revenue', 'Expense'],
            datasets: [{
                label: 'Amount (৳)',
                data: [barData.revenue, barData.expense]
            }]
        }
    });

    // ========== 2. Daily Sales Trend LINE CHART ==========
    new Chart(document.getElementById('dailyLineChart'), {
        type: 'line',
        data: {
            labels: dailySales.map(item => item.date),
            datasets: [{
                label: 'Daily Sales (৳)',
                data: dailySales.map(item => item.total),
                fill: false
            }]
        }
    });

    // ========== 3. Product-wise Sales CHART ==========
    new Chart(document.getElementById('productChart'), {
        type: 'bar',
        data: {
            labels: productSales.map(p => 'Product ' + p.product_id),
            datasets: [{
                label: 'Quantity Sold',
                data: productSales.map(p => p.total_qty)
            }]
        }
    });

    // ========== 4. Profit Growth LINE CHART ==========
    new Chart(document.getElementById('profitLineChart'), {
        type: 'line',
        data: {
            labels: profitData.map(p => p.date),
            datasets: [{
                label: 'Daily Profit (৳)',
                data: profitData.map(p => p.profit),
                fill: true
            }]
        }
    });
</script>


@endsection
