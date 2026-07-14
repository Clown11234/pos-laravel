@extends('layouts.app')

@section('title', __('POS | pos_counter'))
@section('page_title', __('messages.pos_counter'))

@section('content')
    <style>
        .product-card { cursor: pointer; transition: transform 0.2s; }
        .product-card:hover { transform: scale(1.03); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .cart-container { height: 45vh; overflow-y: auto; }
    </style>

    <div class="row g-3">
        <!-- Products Grid Left Side -->
        <div class="col-md-7">
            <div class="card border-0 shadow-sm p-3 mb-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" id="posSearch" class="form-control py-2 border-start-0" placeholder="{{ __('messages.search_placeholder') }}">
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-3 g-3" id="posProductGrid">
                @foreach($products as $product)
                    <div class="col product-item" data-name="{{ strtolower($product->name_en) }} {{ strtolower($product->name_mm) }}" data-code="{{ strtolower($product->product_code) }}">
                        <div class="card h-100 border-0 shadow-sm product-card p-3 text-center"
                             onclick="addToCart({{ $product->id }}, '{{ app()->getLocale() == 'mm' ? $product->name_mm : $product->name_en }}', {{ $product->selling_price }}, {{ $product->stock_quantity }})">
                            <span class="badge bg-secondary position-absolute top-0 start-0 m-2"><i class="fa-solid fa-barcode me-1"></i>{{ $product->product_code }}</span>
                            <div class="mt-3">
                                <h6 class="fw-bold mb-1 text-dark">{{ app()->getLocale() == 'mm' ? $product->name_mm : $product->name_en }}</h6>
                                <p class="text-success fw-bold mb-1">{{ number_format($product->selling_price) }} MMK</p>
                                <small class="text-muted"><i class="fa-solid fa-boxes-stacked me-1"></i>{{ __('messages.stock') }}: {{ $product->stock_quantity }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!--  Cart & Calculation -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100 d-flex flex-column">
                <div class="card-header bg-dark text-white fw-bold py-3">
                    <i class="fa-solid fa-receipt me-2"></i>{{ __('messages.current_order') }}
                </div>

                <div class="card-body cart-container p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="ps-3">{{ __('messages.item') }}</th>
                            <th>{{ __('messages.price') }}</th>
                            <th width="120">{{ __('messages.qty') }}</th>
                            <th class="text-end pe-3">Total</th>
                        </tr>
                        </thead>
                        <tbody id="cartTableBody">
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5"><i class="fa-solid fa-folder-open d-block mb-2 fs-3"></i>{{ __('messages.cart_empty') }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Calculation Footer -->
                <div class="card-footer bg-white border-top p-4 mt-auto">
                    <!-- Subtotal Section -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold text-secondary small">{{ __('messages.total_amount') }}:</span>
                        <span class="fw-semibold text-dark" id="cartSubtotal">0 MMK</span>
                    </div>

                    <!-- Discount Amount -->
                    <div class="row align-items-center mb-3 g-2">
                        <div class="col-6">
                            <label for="discount_amount" class="form-label small fw-bold text-muted mb-0"><i class="fa-solid fa-tag text-danger me-1"></i>{{ __('messages.discount_amount') }}</label>
                        </div>
                        <div class="col-6">
                            <div class="input-group input-group-sm">
                                <input type="number" id="discount_amount" class="form-control text-end fw-bold text-danger" value="0" min="0" placeholder="0">
                                <span class="input-group-text bg-light text-muted small">MMK</span>
                            </div>
                        </div>
                    </div>

                    <!-- Total Section -->
                    <div class="d-flex justify-content-between align-items-center border-top pt-2 mb-3">
                        <h5 class="fw-bold mb-0">{{ __('messages.net_amount')}}</h5>
                        <h4 class="fw-bold text-danger mb-0" id="cartTotal">0 MMK</h4>
                    </div>

                    <!-- Customer Paid Section -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">{{ __('messages.customer_paid_amount')}}</label>
                        <input type="number" id="paid_amount" class="form-control form-control-lg text-end fw-bold text-success" placeholder="0" min="0">
                    </div>

                    <button class="btn btn-success w-100 py-2.5 fw-bold text-uppercase shadow-sm fs-5" onclick="processCheckout()">
                        <i class="fa-solid fa-circle-check me-2"></i>{{ __('messages.proceed_checkout') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Voucher Receipt Modal -->
    <div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body p-4" id="receiptContent"></div>
                <div class="modal-footer border-0 p-2 bg-light d-flex">
                    <button type="button" class="btn btn-secondary flex-fill fw-bold" data-bs-dismiss="modal" onclick="window.location.reload();"><i class="fa-solid fa-xmark me-1"></i> {{ app()->getLocale() == 'mm' ? 'ပိတ်မည်' : 'Close' }}</button>
                    <button type="button" class="btn btn-success flex-fill fw-bold" onclick="window.print()"><i class="fa-solid fa-print me-1"></i> {{ app()->getLocale() == 'mm' ? 'Print ထုတ်မည်' : 'Print' }}</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * { visibility: hidden; }
            #receiptContent, #receiptContent * { visibility: visible; }
            #receiptContent { position: absolute; left: 0; top: 0; width: 100%; font-family: 'Courier New', Courier, monospace; }
            .modal-footer, .btn-close, .btn { display: none !important; }
        }
    </style>
@endsection

@push('scripts')
    <script>
        let cart = [];

        const messages = {
            emptyCart: "{{ __('messages.cart_empty') }}",
            stockOutError: "{{ __('messages.stock_out_error') }}",
            outOfStock: "{{ __('messages.out_of_stock') }}",
            insufficientPaid: "{{ app()->getLocale() == 'mm' ? 'ဟေ့ဟေ့ ပိုက်ဆံလောက်အောင်ပေးလေ!' : 'Insufficient paid amount!' }};"
        };

        function addToCart(id, name, price, maxStock) {
            let existingItem = cart.find(item => item.id === id);

            if (existingItem) {
                if (existingItem.qty >= maxStock) {
                    alert(messages.stockOutError);
                    return;
                }
                existingItem.qty += 1;
            } else {
                if (maxStock <= 0) {
                    alert(messages.outOfStock);
                    return;
                }
                cart.push({ id, name, price, qty: 1, maxStock });
            }
            renderCart();
        }

        function updateQty(id, amount) {
            let item = cart.find(item => item.id === id);
            if (item) {
                item.qty += amount;
                if (item.qty <= 0) {
                    cart = cart.filter(i => i.id !== id);
                } else if (item.qty > item.maxStock) {
                    alert(messages.stockOutError);
                    item.qty = item.maxStock;
                }
            }
            renderCart();
        }

        function calculateCartSubtotal() {
            return cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        }

        function renderCart() {
            const tbody = document.getElementById('cartTableBody');
            const subtotalEl = document.getElementById('cartSubtotal');
            const totalEl = document.getElementById('cartTotal');
            const discountInput = document.getElementById('discount_amount');

            if (cart.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-5"><i class="fa-solid fa-folder-open d-block mb-2 fs-3"></i>${messages.emptyCart}</td></tr>`;
                subtotalEl.innerText = "0 MMK";
                totalEl.innerText = "0 MMK";
                discountInput.value = 0;
                return;
            }

            let html = '';
            let subtotal = calculateCartSubtotal();
            let discount = parseFloat(discountInput.value) || 0;

            // Discount က မူရင်းကျသင့်ငွေထက် မများအောင်လေ
            if (discount > subtotal) {
                discount = subtotal;
                discountInput.value = discount;
            }

            let netTotal = subtotal - discount;

            cart.forEach(item => {
                let itemTotal = item.price * item.qty;
                html += `
                    <tr>
                        <td class="ps-3 fw-semibold text-dark">${item.name}</td>
                        <td class="text-muted">${item.price.toLocaleString()}</td>
                        <td>
                            <div class="input-group input-group-sm">
                                <button class="btn btn-outline-secondary" onclick="updateQty(${item.id}, -1)">-</button>
                                <span class="input-group-text px-3 fw-bold bg-white">${item.qty}</span>
                                <button class="btn btn-outline-secondary" onclick="updateQty(${item.id}, 1)">+</button>
                            </div>
                        </td>
                        <td class="text-end pe-3 fw-bold text-dark">${itemTotal.toLocaleString()} MMK</td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
            subtotalEl.innerText = subtotal.toLocaleString() + " MMK";
            totalEl.innerText = netTotal.toLocaleString() + " MMK";
        }

        // Real Time Calculate Discount !
        document.getElementById('discount_amount').addEventListener('input', renderCart);

        document.getElementById('posSearch').addEventListener('input', function(e) {
            let keyword = e.target.value.toLowerCase();
            document.querySelectorAll('.product-item').forEach(item => {
                let name = item.getAttribute('data-name');
                let code = item.getAttribute('data-code');
                if (name.includes(keyword) || code.includes(keyword)) {
                    item.classList.remove('d-none');
                } else {
                    item.classList.add('d-none');
                }
            });
        });

        function processCheckout() {
            if (cart.length === 0) {
                alert(messages.emptyCart);
                return;
            }

            let subtotal = calculateCartSubtotal();
            let discount = parseFloat(document.getElementById('discount_amount').value) || 0;
            let netTotal = subtotal - discount;
            let paidAmount = parseFloat(document.getElementById('paid_amount').value);

            if (isNaN(paidAmount) || paidAmount < netTotal) {
                alert(messages.insufficientPaid);
                return;
            }

            //  Payload ထဲသို့ discount_amount ထည့်သွင်းလိုက်ခြင်း
            let checkoutData = {
                paid_amount: paidAmount,
                discount_amount: discount,
                items: cart.map(item => {
                    return { id: item.id, qty: item.qty };
                })
            };

            fetch('/pos/checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(checkoutData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        generateReceiptHTML(data.order_id, paidAmount, subtotal, discount, netTotal);
                        cart = [];
                        renderCart();
                        document.getElementById('paid_amount').value = '';
                        document.getElementById('discount_amount').value = '0';

                        let myModal = new bootstrap.Modal(document.getElementById('receiptModal'));
                        myModal.show();
                    } else {
                        alert('ငွေရှင်းရန် မအောင်မြင်ပါ: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('စနစ်ချို့ယွင်းချက်တစ်ခု ဖြစ်ပွားသွားပါသဖြင့် ပြန်လည်ကြိုးစားပါ။');
                });
        }

        function generateReceiptHTML(orderId, paid, subtotal, discount, total) {
            let change = paid - total;
            let itemsHTML = '';

            cart.forEach(item => {
                itemsHTML += `
                    <div class="d-flex justify-content-between font-monospace" style="font-size: 13px;">
                        <span>${item.name} x ${item.qty}</span>
                        <span>${(item.price * item.qty).toLocaleString()} Ks</span>
                    </div>`;
            });

            let receiptBody = `
                <div class="text-center font-monospace">
                    <h5 class="fw-bold mb-0">56 POS Store</h5>
                    <small class="text-muted">Yangon, Myanmar</small>
                    <small class="text-muted">TEL: 09978882960</small>
                    <p class="my-2">-------------------------</p>
                    <small class="d-block text-start">Invoice: #INV-${orderId}</small>
                    <small class="d-block text-start">Date: ${new Date().toLocaleString()}</small>
                    <p class="my-2">-------------------------</p>
                </div>
                ${itemsHTML}
                <div class="font-monospace">
                    <p class="my-2">-------------------------</p>
                    <div class="d-flex justify-content-between text-muted" style="font-size: 13px;">
                        <span>Subtotal:</span><span>${subtotal.toLocaleString()} Ks</span>
                    </div>
                    ${discount > 0 ? `
                    <div class="d-flex justify-content-between text-danger" style="font-size: 13px;">
                        <span>Discount:</span><span>-${discount.toLocaleString()} Ks</span>
                    </div>` : ''}
                    <div class="d-flex justify-content-between fw-bold fs-6 border-top pt-1 mt-1">
                        <span>Net Total:</span><span>${total.toLocaleString()} Ks</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted" style="font-size: 13px; mt-1">
                        <span>Paid:</span><span>${paid.toLocaleString()} Ks</span>
                    </div>
                    <div class="d-flex justify-content-between text-success fw-bold">
                        <span>Change:</span><span>${change.toLocaleString()} Ks</span>
                    </div>
                    <p class="my-2">-------------------------</p>
                    <h6 class="text-center fw-bold mt-3">{{ __('messages.thank_you') }}</h6>
                </div>
            `;

            document.getElementById('receiptContent').innerHTML = receiptBody;
        }
    </script>
@endpush
