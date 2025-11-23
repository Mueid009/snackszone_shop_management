@extends('layouts.app')

@section('content')
<style>
    :root{
        --primary-1: #E2852E; /* main orange */
        --primary-2: #F5C857; /* warm yellow */
        --accent-1: #FFEE91;  /* pale highlight */
        --accent-2: #ABE0F0;  /* light blue */
        --card-bg: #ffffff;
        --radius: 16px;
        --muted: #6b6b6b;
    }

    .dashboard-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        padding: 18px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.06);
    }

    .top-value { font-size:1.6rem; font-weight:700; color: #222; }
    .sub-text  { font-size:0.9rem; color:var(--muted); }

    .circle-icon {
        width:52px; height:52px; border-radius:50%;
        display:flex; align-items:center; justify-content:center;
        color:#fff; font-weight:700; font-size:1.2rem;
    }
    .grad-orange { background: linear-gradient(135deg, var(--primary-1), var(--primary-2)); }
    .grad-yellow { background: linear-gradient(135deg, var(--primary-2), var(--accent-1)); }
    .grad-blue   { background: linear-gradient(135deg, var(--accent-2), #6fc3e8); }

    .chart-box { min-height:280px; }
    #revenueChart{ width:100% !important; height:260px !important; }
    #donutChart{ width:100% !important; height:220px !important; }

    /* Dynamic cart */
    .cart-card { position: sticky; top:18px; }
    .cart-items { max-height:240px; overflow:auto; }
    .cart-row { display:flex; justify-content:space-between; gap:10px; align-items:center; padding:8px 0; border-bottom:1px dashed #eee; }

    @media (max-width: 900px){
        .chart-box{ height:auto; }
    }
</style>

<div class="container-fluid px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 " style="gap:12px; background-color:#1E3A8A; border-radius: 10px; padding: 12px 18px;">
        <div class="d-flex align-items-center" style="gap:12px;">
            <h3 style="margin:0; color:#F5C857;">Dashboard Overview</h3>
        </div>

        <form method="GET" action="">
            <select name="filter" onchange="this.form.submit()" class="form-select shadow-sm"
                style="width:200px; border-radius:10px; border:2px solid var(--primary-1);">
                <option value="today"  {{ $filter == 'today' ? 'selected' : '' }}>Today</option>
                <option value="week"   {{ $filter == 'week' ? 'selected' : '' }}>This Week</option>
                <option value="month"  {{ $filter == 'month' ? 'selected' : '' }}>This Month</option>
                <option value="year"   {{ $filter == 'year' ? 'selected' : '' }}>This Year</option>
            </select>
        </form>
    </div>

    <!-- top cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="sub-text">Filtered Revenue</div>
                        <div class="top-value">{{ number_format($filteredRevenue,2) }}৳</div>
                        <div class="sub-text">{{ ucfirst($filter) }}</div>
                    </div>
                    <div class="circle-icon grad-orange">৳</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="sub-text">Orders</div>
                        <div class="top-value">{{ $totalOrders }}</div>
                        <div class="sub-text">{{ ucfirst($filter) }}</div>
                    </div>
                    <div class="circle-icon grad-blue">🛒</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="sub-text">Total Items</div>
                        <div class="top-value">{{ number_format($totalItems) }}</div>
                        <div class="sub-text">In Catalog</div>
                    </div>
                    <div class="circle-icon grad-yellow">📦</div>
                </div>
            </div>
        </div>
    </div>

    <!-- second row -->
    <div class="row g-4 mb-3">
        <div class="col-lg-4">
            <div class="dashboard-card">
                <div class="sub-text">Total Revenue</div>
                <div class="top-value">{{ number_format($totalRevenue,2) }}৳</div>
                <div class="sub-text">All time</div>
            </div>
            
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card">
                <div class="sub-text">Expenses</div>
                <div class="top-value">{{ number_format($totalExpenses,2) }}৳</div>
                <div class="sub-text">-</div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card">
                <h6 class="fw-bold mb-3">Top Selling Products</h6>
                @if($topSelling->count())
                    @foreach($topSelling as $t)
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <div>{{ $t->product_name ?? 'N/A' }}</div>
                            <strong>{{ (int)$t->total_qty }}</strong>
                        </div>
                    @endforeach
                @else
                    <div class="text-muted small">No sales yet.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- bottom: charts + cart + low stock -->
    <div class="row g-4">

        <div class="col-lg-7">
            <div class="dashboard-card chart-box">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Monthly Sales ({{ date('Y') }})</h5>
                    
                </div>
                <canvas id="revenueChart"></canvas>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <h6 class="mb-2">Inventory Health</h6>
                        <canvas id="donutChart"></canvas>
                    </div>

                    <div class="col-md-6">
                        <h6 class="mb-2">Low Stock Items</h6>
                        <div style="max-height:160px; overflow:auto;">
                            @if($lowStockItems->count())
                                @foreach($lowStockItems as $p)
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <div>{{ $p->product_name }}</div>
                                        <strong class="text-danger">{{ $p->stock }}</strong>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-muted small">No low stock items.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- right column: dynamic cart + quick links -->
        <div class="col-lg-5">
            <div class="dashboard-card cart-card">
                <h5 class="mb-3">Quick Cart</h5>

                <div class="mb-2">
                    <label class="small">Product</label>
                    <select id="cartProduct" class="form-select mb-2">
                        <option value="">-- select product --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" data-price="{{ $p->price }}" data-stock="{{ $p->stock }}">
                                {{ $p->product_name }} ({{ $p->stock }} in stock) — {{ number_format($p->price,2) }}৳
                            </option>
                        @endforeach
                    </select>

                    <div class="d-flex gap-2">
                        <input id="cartQty" type="number" min="1" value="1" class="form-control" placeholder="Qty">
                        <button id="addToCartBtn" class="btn btn-primary">Add</button>
                    </div>
                </div>

                <div class="cart-items mt-3 mb-2" id="cartItemsList">
                    <!-- dynamic rows -->
                    <div class="text-muted small">No items in cart.</div>
                </div>

                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="sub-text">Subtotal</div>
                        <div id="cartSub" class="top-value">0.00৳</div>
                    </div>
                    <div style="min-width:160px;">
                        <button id="clearCart" class="btn btn-outline-secondary w-100 mb-2">Clear</button>
                        <button id="exportCart" class="btn" style="background:linear-gradient(90deg,var(--primary-1),var(--primary-2)); color:#fff; width:100%;">Export (JSON)</button>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('products.index') }}" class="btn btn-light w-100 mb-2">Manage Products</a>
                    <a href="{{ route('pos.create') }}" class="btn btn-outline-primary w-100">Open POS</a>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Monthly data from server
    const monthlyObj = @json($monthly);
    const labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const values = labels.map((_,i)=> monthlyObj[i+1] || 0);

    // generate a palette of warm/cool colors for 12 bars
    const barColors = [
        '#FF6B35','#FF8A4B','#FFA86B','#FFC78A','#FFDDA8','#FFEFC8',
        '#AEE2F5','#7FD0F0','#5FBCEB','#3FB0E1','#1F98D6','#0F78C2'
    ];

    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Revenue',
                data: values,
                backgroundColor: barColors,
                borderColor: barColors.map(c => shadeColor(c, -10)),
                borderWidth: 1
            }]
        },
        options: {
            plugins: { legend: { display:false } },
            scales: {
                y: { beginAtZero:true, ticks: { callback: v => v } }
            }
        }
    });

    // donut chart for in-stock percent
    const inStockPercent = parseFloat('{{ $inStockPercent }}') || 0;
    const donutCtx = document.getElementById('donutChart').getContext('2d');
    const donutChart = new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: ['In Stock %', 'Out of Stock %'],
            datasets: [{
                data: [inStockPercent, Math.max(0, 100 - inStockPercent)],
                backgroundColor: ['#4CD4A9','#FFB199'],
                hoverOffset: 6
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' },
                tooltip: { callbacks: { label: ctx => ctx.label + ': ' + ctx.parsed + '%' } }
            },
            cutout: '65%'
        }
    });

    // small helper to slightly darken borders
    function shadeColor(hex, percent) {
        let f=parseInt(hex.slice(1),16),t=percent<0?0:255,p= Math.abs(percent)/100;
        let R=f>>16, G=f>>8&0x00FF, B=f&0x0000FF;
        let r = Math.round((t-R)*p)+R;
        let g = Math.round((t-G)*p)+G;
        let b = Math.round((t-B)*p)+B;
        return "#" + (0x1000000 + (r<<16) + (g<<8) + b).toString(16).slice(1);
    }

    // ---------------- Dynamic cart (client-side) ----------------
    const products = @json($products);
    let cart = [];

    const productSelect = document.getElementById('cartProduct');
    const qtyInput = document.getElementById('cartQty');
    const addBtn = document.getElementById('addToCartBtn');
    const cartList = document.getElementById('cartItemsList');
    const cartSub = document.getElementById('cartSub');
    const clearCartBtn = document.getElementById('clearCart');
    const exportBtn = document.getElementById('exportCart');

    addBtn.addEventListener('click', () => {
        const pid = parseInt(productSelect.value);
        const qty = Math.max(1, parseInt(qtyInput.value || 1));
        if (!pid) { alert('Select a product'); return; }
        const prod = products.find(p => p.id === pid);
        if (!prod) { alert('Product not found'); return; }
        if (prod.stock < qty) {
            alert('Not enough stock. Available: ' + prod.stock);
            return;
        }

        // if exists, increase qty
        const existing = cart.find(c => c.id === pid);
        if (existing) {
            if (prod.stock < existing.qty + qty) {
                alert('Cannot add — exceeds available stock.');
                return;
            }
            existing.qty += qty;
            existing.subtotal = existing.qty * existing.price;
        } else {
            cart.push({ id:prod.id, name:prod.product_name, price: parseFloat(prod.price), qty, subtotal: qty * parseFloat(prod.price) });
        }

        renderCart();
    });

    function renderCart(){
        cartList.innerHTML = '';
        if (cart.length === 0) {
            cartList.innerHTML = '<div class="text-muted small">No items in cart.</div>';
            cartSub.innerText = '0.00৳';
            return;
        }
        let total = 0;
        cart.forEach((c, idx) => {
            total += c.subtotal;
            const row = document.createElement('div');
            row.className = 'cart-row';
            row.innerHTML = `
                <div style="flex:1;">
                    <div><strong>${escapeHtml(c.name)}</strong></div>
                    <div class="small text-muted">${c.qty} × ${c.price.toFixed(2)}৳</div>
                </div>
                <div style="min-width:85px; text-align:right;">
                    <div>${c.subtotal.toFixed(2)}৳</div>
                    <div class="mt-1">
                        <button data-idx="${idx}" class="btn btn-sm btn-outline-secondary edit-item">✏️</button>
                        <button data-idx="${idx}" class="btn btn-sm btn-outline-danger remove-item">🗑️</button>
                    </div>
                </div>
            `;
            cartList.appendChild(row);
        });
        cartSub.innerText = total.toFixed(2) + '৳';

        // hook edit/remove
        cartList.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', e => {
                const i = parseInt(e.currentTarget.dataset.idx);
                cart.splice(i,1);
                renderCart();
            });
        });
        cartList.querySelectorAll('.edit-item').forEach(btn => {
            btn.addEventListener('click', e => {
                const i = parseInt(e.currentTarget.dataset.idx);
                const newQty = parseInt(prompt('New quantity for ' + cart[i].name, cart[i].qty));
                if (!newQty || newQty < 1) return;
                const prod = products.find(p => p.id === cart[i].id);
                if (newQty > prod.stock) { alert('Not enough stock'); return; }
                cart[i].qty = newQty;
                cart[i].subtotal = cart[i].qty * cart[i].price;
                renderCart();
            });
        });
    }

    clearCartBtn.addEventListener('click', () => {
        if (!confirm('Clear cart?')) return;
        cart = [];
        renderCart();
    });

    exportBtn.addEventListener('click', () => {
        // Download cart JSON
        const data = { created_at: new Date(), items: cart, total: cart.reduce((s,c)=>s+c.subtotal,0) };
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'cart-'+Date.now()+'.json';
        a.click();
        URL.revokeObjectURL(url);
    });

    // escape helper
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // initialize
    renderCart();

</script>
@endsection
