<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.pos_counter') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .product-card { cursor: pointer; transition: transform 0.2s; }
        .product-card:hover { transform: scale(1.03); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .cart-container { height: 50vh; overflow-y: auto; }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 px-3">
        <div>
            <h3 class="fw-bold text-dark mb-0"><i class="fa-solid fa-cash-register me-2"></i>{{ __('messages.pos_counter') }}</h3>
        </div>
        <div class="d-flex align-items-center">
            <a href="{{ route('lang.switch', 'en') }}" class="btn btn-sm btn-outline-primary me-1 @if(app()->getLocale() == 'en') active @endif">🇬🇧 English</a>
            <a href="{{ route('lang.switch', 'mm') }}" class="btn btn-sm btn-outline-primary me-3 @if(app()->getLocale() == 'mm') active @endif">🇲🇲 မြန်မာ</a>

            <div class="d-flex align-items-center me-3 bg-white px-3 py-1.5 rounded-3 shadow-sm border">
                <span class="text-secondary fw-semibold me-3"><i class="fa-solid fa-user me-1"></i> {{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline" onsubmit="return confirm('အကောင့်ကနေ ထွက်မှာ သေჩာပါသလား?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger fw-bold rounded-2">
                        <i class="fa-solid fa-right-from-bracket me-1"></i>{{ app()->getLocale() == 'mm' ? 'ထွက်ရန်' : 'Logout' }}
                    </button>
                </form>
            </div>

            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary fw-semibold">
                <i class="fa-solid fa-arrow-left me-1"></i> {{ __('messages.back_to_products') }}
            </a>
        </div>
    </div>

    <div class="row g-3">
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

                <div class="card-footer bg-white border-top p-4 mt-auto">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">{{ __('messages.total_amount') }}:</h5>
                        <h4 class="fw-bold text-danger mb-0" id="cartTotal">0 MMK</h4>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary"><i class="fa-solid fa-money-bill-wave me-1"></i>{{ app()->getLocale() == 'mm' ? 'ဝယ်သူပေးငွေ (Paid Amount)' : 'Customer Paid' }}</label>
                        <input type="number" id="paid_amount" class="form-control form-control-lg text-end fw-bold text-success" placeholder="0" min="0">
                    </div>

                    <button class="btn btn-success w-100 py-2.5 fw-bold text-uppercase shadow-sm fs-5" onclick="processCheckout()">
                        <i class="fa-solid fa-circle-check me-2"></i>{{ __('messages.proceed_checkout') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body p-4" id="receiptContent"></div>
            <div class="modal-footer border-0 p-2 bg-light d-flex">
                <button type="button" class="btn btn-secondary flex-fill fw-bold" data-bs-dismiss="modal" onclick="window.location.reload();"><i class="fa-solid fa-xmark me-1"></i> ပိတ်မည်</button>
                <button type="button" class="btn btn-success flex-fill fw-bold" onclick="window.print()"><i class="fa-solid fa-print me-1"></i> Print ထုတ်မည်</button>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let cart = [];

    const messages = {
        emptyCart: "{{ __('messages.cart_empty') }}",
        stockOutError: "{{ __('messages.stock_out_error') }}",
        outOfStock: "{{ __('messages.out_of_stock') }}",
        insufficientPaid: "{{ app()->getLocale() == 'mm' ? 'ဟေ့ဟေ့ ပိုက်ဆံလောက်အောင်ပေးလေ!' : 'Insufficient paid amount!' }}"
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

    function calculateCartTotal() {
        return cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    }

    function renderCart() {
        const tbody = document.getElementById('cartTableBody');
        const totalEl = document.getElementById('cartTotal');

        if (cart.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-5"><i class="fa-solid fa-folder-open d-block mb-2 fs-3"></i>${messages.emptyCart}</td></tr>`;
            totalEl.innerText = "0 MMK";
            return;
        }

        let html = '';
        let grandTotal = calculateCartTotal();

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
        totalEl.innerText = grandTotal.toLocaleString() + " MMK";
    }

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

        let paidAmount = parseFloat(document.getElementById('paid_amount').value);
        let totalAmount = calculateCartTotal();

        if (isNaN(paidAmount) || paidAmount < totalAmount) {
            alert(messages.insufficientPaid);
            return;
        }

        let checkoutData = {
            paid_amount: paidAmount,
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
                    generateReceiptHTML(data.order_id, paidAmount, totalAmount);
                    cart = [];
                    renderCart();
                    document.getElementById('paid_amount').value = '';

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

    function generateReceiptHTML(orderId, paid, total) {
        let change = paid - total;
        let isMyanmar = "{{ app()->getLocale() }}" === 'mm';
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
                <h5 class="fw-bold mb-0">POS SUPERMARKET</h5>
                <small class="text-muted">Yangon, Myanmar</small>
                <p class="my-2">--------------------------------</p>
                <small class="d-block text-start">Invoice: #INV-${orderId}</small>
                <small class="d-block text-start">Date: ${new Date().toLocaleString()}</small>
                <p class="my-2">--------------------------------</p>
            </div>
            ${itemsHTML}
            <div class="font-monospace">
                <p class="my-2">--------------------------------</p>
                <div class="d-flex justify-content-between fw-bold fs-6">
                    <span>Total:</span><span>${total.toLocaleString()} Ks</span>
                </div>
                <div class="d-flex justify-content-between text-muted" style="font-size: 13px;">
                    <span>Paid:</span><span>${paid.toLocaleString()} Ks</span>
                </div>
                <div class="d-flex justify-content-between text-success fw-bold">
                    <span>Change:</span><span>${change.toLocaleString()} Ks</span>
                </div>
                <p class="my-2">--------------------------------</p>
                <h6 class="text-center fw-bold mt-3">${isMyanmar ? 'ကျေးဇူးတင်ပါသည်၊ ထပ်လာခဲ့ပါဦး' : 'Thank you! Come Again.'}</h6>
            </div>
        `;

        document.getElementById('receiptContent').innerHTML = receiptBody;
    }
</script>
</body>
</html>
