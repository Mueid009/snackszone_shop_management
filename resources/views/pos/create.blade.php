@extends('layouts.app')

@section('content')

<div class="container">
    <h2>Create POS / Order</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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

        <div class="card mb-3">
    <div class="card-header">Products</div>
    <div class="card-body">
        <table class="table table-sm" id="dynamicProductsTable">
            <thead>
                <tr>
                    <th>Product</th>
                    <th style="width:90px">Qty</th>
                    <th style="width:140px">Price</th>
                    <th style="width:140px">Subtotal</th>
                    <th style="width:60px"></th>
                </tr>
            </thead>
            <tbody>
                <!-- initial row (select-based) -->
                <tr>
                    <td>
                        <select class="form-control product-select">
                            <option value="">-- select product --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}"
                                        data-price="{{ $p->price }}"
                                        data-stock="{{ $p->stock }}">
                                    {{ $p->product_name }} @if($p->stock <= 0) (Out of stock) @endif
                                </option>
                            @endforeach
                        </select>

                        <!-- hidden inputs submitted to server -->
                        <input type="hidden" name="items[][product_id]" class="input-product-id">
                        <input type="hidden" name="items[][product_name]" class="input-product-name">
                    </td>

                    <td><input type="number" name="items[][quantity]" class="form-control input-qty" value="1" min="1"></td>
                    <td><input type="number" step="0.01" name="items[][unit_price]" class="form-control input-unit-price" value="0.00"></td>
                    <td><span class="row-subtotal">0.00</span></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
                </tr>
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center">
            <div>
                <button type="button" id="addDynamicProductBtn" class="btn btn-primary btn-sm">Add product</button>
            </div>
            <div>
                <strong>Grand Total: </strong> <span id="dynamicGrandTotal">0.00</span>
            </div>
            <div class="mt-3 text-end">
                <div class="d-flex justify-content-end align-items-center mb-2">
                    <label class="me-2 fw-bold">Discount:</label>
                    <input type="number" id="discountInput" name="discount" class="form-control w-25" value="0" min="0">
                </div>

                <div>
                    <strong>Payable Total: </strong>
                    <span id="payableTotal">0.00</span>
                </div>
            </div>
        </div>

        <!-- template for new row -->
        <template id="dynamicRowTpl">
            <tr>
                <td>
                    <select class="form-control product-select">
                        <option value="">-- select product --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}"
                                    data-price="{{ $p->price }}"
                                    data-stock="{{ $p->stock }}">
                                {{ $p->product_name }} @if($p->stock <= 0) (Out of stock) @endif
                            </option>
                        @endforeach
                    </select>

                    <input type="hidden" name="items[][product_id]" class="input-product-id">
                    <input type="hidden" name="items[][product_name]" class="input-product-name">
                </td>
                <td><input type="number" name="items[][quantity]" class="form-control input-qty" value="1" min="1"></td>
                <td><input type="number" step="0.01" name="items[][unit_price]" class="form-control input-unit-price" value="0.00"></td>
                <td><span class="row-subtotal">0.00</span></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
            </tr>
        </template>
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
    const tbody = document.querySelector('#dynamicProductsTable tbody');
    const addBtn = document.getElementById('addDynamicProductBtn');
    const tpl = document.getElementById('dynamicRowTpl');
    const grandTotalEl = document.getElementById('dynamicGrandTotal');

    if (!tbody || !addBtn || !tpl || !grandTotalEl) {
        console.warn('POS dynamic: missing required DOM nodes');
        return;
    }

    function fmt(n){ return Number(n).toFixed(2); }

    function recalc() {
        let total = 0;
        tbody.querySelectorAll('tr').forEach(row => {
            const qty = parseFloat(row.querySelector('.input-qty').value || 0) || 0;
            const price = parseFloat(row.querySelector('.input-unit_price')?.value || row.querySelector('.input-unit-price').value || 0) || 0;
            const subtotal = qty * price;
            const subEl = row.querySelector('.row-subtotal');
            if (subEl) subEl.textContent = fmt(subtotal);
            total += subtotal;
        });
        grandTotalEl.textContent = fmt(total);
    }

    // ---------- PAYABLE TOTAL: grandTotal - discount (minimal change) ----------
    const discountInput = document.getElementById('discountInput');
    const payableTotalEl = document.getElementById('payableTotal');

    function updatePayable() {
        // read grand total text (already formatted like "123.45")
        let grand = parseFloat((grandTotalEl.textContent || '0').replace(/,/g, '')) || 0;
        // read discount (coerce to number)
        let discount = parseFloat(discountInput.value || '0') || 0;

        // sanitize: no negative discount, discount can't exceed grand total
        if (discount < 0) discount = 0;
        if (discount > grand) discount = grand;

        const payable = Math.max(0, grand - discount);
        payableTotalEl.textContent = fmt(payable);
    }

    // update when discount changes
    if (discountInput) discountInput.addEventListener('input', updatePayable);

    // watch grand total changes (non-invasive) and update payable automatically
    const grandObserver = new MutationObserver(updatePayable);
    if (grandTotalEl) grandObserver.observe(grandTotalEl, { childList: true, characterData: true, subtree: true });

    // initial sync
    updatePayable();

    function syncSelect(select) {
        const row = select.closest('tr');
        if (!row) return;
        const opt = select.options[select.selectedIndex];
        const pid = row.querySelector('.input-product-id');
        const pname = row.querySelector('.input-product-name');
        const priceEl = row.querySelector('.input-unit-price');
        if (pid) pid.value = select.value || '';
        if (pname) pname.value = opt ? opt.text : '';
        if (priceEl) priceEl.value = fmt(parseFloat(opt ? (opt.dataset.price || 0) : 0));
    }

    function wireRow(row) {
        if (!row) return;
        const select = row.querySelector('.product-select');
        const qty = row.querySelector('.input-qty');
        const price = row.querySelector('.input-unit-price');
        const removeBtn = row.querySelector('.remove-row');

        if (select) {
            // on change, fill hidden inputs and price
            select.addEventListener('change', function(){
                syncSelect(select);
                // manage stock: if out of stock, disable qty
                const opt = select.options[select.selectedIndex];
                const stock = opt ? parseInt(opt.dataset.stock || '0', 10) : 0;
                if (qty) {
                    if (stock <= 0) {
                        qty.value = 0;
                        qty.setAttribute('readonly', 'readonly');   // <-- NEW
                        qty.classList.add('text-muted');            // optional
                    } else {
                        if (parseInt(qty.value || '0', 10) < 1) qty.value = 1;
                        qty.removeAttribute('readonly');            // <-- NEW
                        qty.classList.remove('text-muted');         // optional

                        if (parseInt(qty.value,10) > stock) qty.value = stock;
                    }
                }
                recalc();
            });
        }

        if (qty) qty.addEventListener('input', function(){
            // optional: clamp to stock if select chosen
            const sel = row.querySelector('.product-select');
            if (sel) {
                const opt = sel.options[sel.selectedIndex];
                const stock = opt ? parseInt(opt.dataset.stock || '0', 10) : 0;
                if (stock > 0 && parseInt(qty.value || '0', 10) > stock) qty.value = stock;
            }
            recalc();
        });

        if (price) price.addEventListener('input', recalc);

        if (removeBtn) removeBtn.addEventListener('click', function(){
            row.remove();
            recalc();
        });

        // initial sync if select already chosen
        if (select && select.value) syncSelect(select);
    }

    // wire existing rows
    tbody.querySelectorAll('tr').forEach(r => wireRow(r));
    recalc();

    addBtn.addEventListener('click', function(){
        // clone template
        let clone;
        if (tpl.content) {
            clone = tpl.content.cloneNode(true);
            tbody.appendChild(clone);
            const newRow = tbody.lastElementChild;
            wireRow(newRow);
            const focusEl = newRow.querySelector('.product-select') || newRow.querySelector('.input-product-name');
            if (focusEl) focusEl.focus();
        } else {
            tbody.insertAdjacentHTML('beforeend', tpl.innerHTML || '');
            const newRow = tbody.lastElementChild;
            wireRow(newRow);
            const focusEl = newRow.querySelector('.product-select') || newRow.querySelector('.input-product-name');
            if (focusEl) focusEl.focus();
        }
        recalc();
    });

    document.getElementById('posForm').addEventListener('submit', function(e) {
    // remove any previously inserted auto-hidden items to avoid duplicates
        document.querySelectorAll('input[name^="items["]').forEach(n => {
            // only remove inputs we previously injected (we will mark them)
            if (n.dataset.autogen === "1") n.remove();
        });

        const tbody = document.querySelector('#dynamicProductsTable tbody') || document.querySelector('#simpleProductsTable tbody');
        if (!tbody) return; // nothing to do

        const rows = Array.from(tbody.querySelectorAll('tr'));
        const cleanItems = [];

        rows.forEach((row, idx) => {
            // read from the row elements (select OR product name input)
            const select = row.querySelector('.product-select');
            const pidHidden = row.querySelector('.input-product-id'); // may exist
            const pnameHidden = row.querySelector('.input-product-name'); // may exist
            const pnameInput = row.querySelector('.input-product-name') || row.querySelector('.input-product-name-fallback'); // fallback
            const qtyInput = row.querySelector('.input-qty');
            const priceInput = row.querySelector('.input-unit-price');

            // determine values (prefer explicit inputs; fallback to select data)
            const product_id = select ? (select.value || (pidHidden ? pidHidden.value : '')) : (pidHidden ? pidHidden.value : '');
            const product_name = (pnameHidden && pnameHidden.value && pnameHidden.value.trim() !== '') ? pnameHidden.value.trim()
                                : (select && select.options[select.selectedIndex] ? select.options[select.selectedIndex].text.trim() : (pnameInput ? pnameInput.value.trim() : ''));

            // ensure qty and price values (coerce)
            let quantity = 0;
            if (qtyInput) {
                // remove disabled if accidentally set so it will submit
                if (qtyInput.hasAttribute('disabled')) qtyInput.removeAttribute('disabled');
                quantity = parseInt(qtyInput.value || '0', 10) || 0;
            }

            let unit_price = 0.0;
            if (priceInput) unit_price = parseFloat(priceInput.value || '0') || 0.0;
            // if price is zero and select has data-price, use that
            if ((unit_price === 0 || isNaN(unit_price)) && select) {
                unit_price = parseFloat(select.options[select.selectedIndex]?.dataset?.price || 0) || 0.0;
            }

            // skip empty rows (no product name or zero qty)
            if (!product_name || product_name === '' || quantity < 1) {
                return; // continue
            }

            cleanItems.push({
                product_id: product_id || null,
                product_name: product_name,
                quantity: quantity,
                unit_price: unit_price
            });

            // Inject hidden inputs for this item so Laravel receives items[IDX][field]
            // We mark them with data-autogen="1" so we can remove them on next submit.
            const form = this; // current form element
            const fields = ['product_id','product_name','quantity','unit_price'];
            fields.forEach(field => {
                const val = ({
                    product_id: product_id || '',
                    product_name: product_name,
                    quantity: quantity,
                    unit_price: unit_price
                })[field];

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `items[${cleanItems.length - 1}][${field}]`;
                input.value = val;
                input.dataset.autogen = "1";
                form.appendChild(input);
            });
        });

        if (cleanItems.length === 0) {
            e.preventDefault();
            alert('At least one product with quantity >= 1 is required. দয়া করে একটি প্রোডাক্ট যোগ করে পরিমাণ ঠিক করে সাবমিট করুন।');
            return false;
        }

        // All hidden inputs are appended; allow normal submit to continue.
    });
});
</script>
@endsection