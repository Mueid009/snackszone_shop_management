@extends('layouts.app')

@section('content')

<div class="container">
    <h2>Create POS / Order</h2>

    <form action="{{ route('pos.store') }}" method="POST" id="posForm">
        @csrf

        <!-- Customer Info -->
        <div class="card mb-3">
            <div class="card-header">Customer Info</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label>Customer Name</label>
                        <input type="text" name="customer_name" class="form-control">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label>Customer Phone</label>
                        <input type="text" name="customer_phone" class="form-control">
                    </div>
                    <div class="col-12 mb-2">
                        <label>Customer Address</label>
                        <textarea name="customer_address" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-12">
                        <label>Order Description</label>
                        <input type="text" name="order_description" class="form-control">
                    </div>
                </div>
            </div>
        </div>

       <!-- product row -->
        <div class="card mb-3">
            <div class="card-header">Order Info</div>
                <table>
                    <tbody id="productRowTemplate">
                        <tr>
                            <td>
                                <select class="form-control product-select">
                                    <option value="">-- choose product --</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}"
                                                data-price="{{ $p->price }}"
                                                data-stock="{{ $p->stock }}"
                                                @if($p->stock <= 0) disabled @endif
                                        >
                                            {{ $p->product_name }} @if($p->stock <= 0) (Out of stock) @else (Stock: {{ $p->stock }}) @endif
                                        </option>
                                    @endforeach
                                </select>

                                <!-- hidden fields that JS will populate -->
                                <input type="hidden" name="items[][product_id]" class="input-product-id">
                                <input type="hidden" name="items[][product_name]" class="input-product-name">
                            </td>
                            <td><input type="number" name="items[][quantity]" class="form-control input-qty" value="1" min="1"></td>
                            <td><input type="number" step="0.01" name="items[][unit_price]" class="form-control input-unit-price" value="0"></td>
                            <td><span class="row-subtotal">0.00</span></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


                <!-- Payment method -->
                <div class="card mb-3">
                    <div class="card-header">Payment Method</div>
                    <div class="card-body">
                        <div class="btn-group" role="group" aria-label="payment">
                            <label class="btn btn-outline-secondary">
                                <input type="radio" name="payment_method" value="Bikash"> Bikash
                            </label>
                            <label class="btn btn-outline-secondary">
                                <input type="radio" name="payment_method" value="Nogod"> Nogod
                            </label>
                            <label class="btn btn-outline-secondary">
                                <input type="radio" name="payment_method" value="Card"> Card
                            </label>
                            <label class="btn btn-outline-secondary">
                                <input type="radio" name="payment_method" value="Hand Cash"> Hand Cash
                            </label>
                        </div>
                    </div>
                </div>

                <button class="btn btn-success" type="submit">Create Order</button>
            </form>
        </div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.querySelector('#productsTable tbody');
    const addBtn = document.getElementById('addProductBtn');
    const templateHTML = document.getElementById('productRowTemplate').innerHTML;
    const grandTotalEl = document.getElementById('grandTotal');

    // helpers
    function formatNumber(n){ return Number(n).toFixed(2); }

    function recalc() {
        // compute each row subtotal, then grand total (digit-by-digit safe)
        let total = 0;
        tbody.querySelectorAll('tr').forEach(row => {
            const qtyEl = row.querySelector('.input-qty');
            const priceEl = row.querySelector('.input-unit-price');
            const subtotalEl = row.querySelector('.row-subtotal');

            const qty = parseInt(qtyEl.value === '' ? '0' : qtyEl.value, 10) || 0;
            const price = parseFloat(priceEl.value === '' ? '0' : priceEl.value) || 0.0;
            const subtotal = qty * price;

            subtotalEl.textContent = formatNumber(subtotal);
            total += subtotal;
        });

        grandTotalEl.textContent = formatNumber(total);
    }

    // fill hidden inputs product_id & name from select
    function syncSelectToHidden(select) {
        const row = select.closest('tr');
        const pidInput = row.querySelector('.input-product-id');
        const pnameInput = row.querySelector('.input-product-name');
        const opt = select.options[select.selectedIndex];
        pidInput.value = select.value || '';
        pnameInput.value = opt ? opt.text : '';
    }

    function wireRow(row) {
        const select = row.querySelector('.product-select');
        const qty = row.querySelector('.input-qty');
        const price = row.querySelector('.input-unit-price');
        const removeBtn = row.querySelector('.remove-row');

        if (select) {
            // sync initial selection if any
            syncSelectToHidden(select);

            select.addEventListener('change', function() {
                const opt = select.options[select.selectedIndex];
                if (!opt) return;
                const priceVal = parseFloat(opt.dataset.price || '0') || 0;
                const stock = parseInt(opt.dataset.stock || '0') || 0;

                price.value = formatNumber(priceVal);
                syncSelectToHidden(select);

                // clamp qty with stock
                if (stock > 0) {
                    if (parseInt(qty.value || '0', 10) < 1) qty.value = 1;
                    if (parseInt(qty.value || '0', 10) > stock) qty.value = stock;
                    qty.removeAttribute('disabled');
                } else {
                    qty.value = 0;
                    qty.setAttribute('disabled', 'disabled');
                }
                recalc();
            });
        }

        if (qty) {
            qty.addEventListener('input', function() {
                const v = parseInt(qty.value || '0', 10);
                if (!qty.disabled) {
                    if (isNaN(v) || v < 1) qty.value = 1;
                }
                recalc();
            });
        }

        if (price) price.addEventListener('input', recalc);

        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                row.remove();
                recalc();
            });
        }
    }

    // wire already-existing rows (e.g., when editing)
    tbody.querySelectorAll('tr').forEach(r => wireRow(r));
    recalc();

    addBtn.addEventListener('click', function() {
        tbody.insertAdjacentHTML('beforeend', templateHTML);
        const newRow = tbody.lastElementChild;
        wireRow(newRow);
        recalc();
        // focus on the new product select for quick input
        const sel = newRow.querySelector('.product-select');
        if (sel) sel.focus();
    });

    // before submit: ensure every row has product_id filled (select sync) and validate quantities
    const form = document.getElementById('posForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            let invalid = false;
            tbody.querySelectorAll('tr').forEach(row => {
                const select = row.querySelector('.product-select');
                const qty = row.querySelector('.input-qty');

                // sync select to hidden inputs
                if (select) syncSelectToHidden(select);

                // validate
                if (!select || !select.value) {
                    invalid = true;
                    select.classList.add('is-invalid');
                } else select.classList.remove('is-invalid');

                if (qty && !qty.disabled) {
                    const q = parseInt(qty.value || '0', 10);
                    if (isNaN(q) || q < 1) {
                        invalid = true;
                        qty.classList.add('is-invalid');
                    } else qty.classList.remove('is-invalid');
                }
            });

            if (invalid) {
                e.preventDefault();
                alert('Please fix product selection and quantities before submitting.');
            }
        });
    }
});
</script>
@endsection