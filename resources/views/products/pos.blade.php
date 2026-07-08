<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.pos_counter') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .product-card { cursor: pointer; transition: transform 0.2s; }
        .product-card:hover { transform: scale(1.03); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .cart-container { height: 60vh; overflow-y: auto; }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 px-3">
        <div>
            <h3 class="fw-bold text-dark mb-0">🛒 {{ __('messages.pos_counter') }}</h3>
        </div>
        <div class="d-flex align-items-center">
            <a href="{{ route('lang.switch', 'en') }}" class="btn btn-sm btn-outline-primary me-1 @if(app()->getLocale() == 'en') active @endif">🇬🇧 English</a>
            <a href="{{ route('lang.switch', 'mm') }}" class="btn btn-sm btn-outline-primary me-3 @if(app()->getLocale() == 'mm') active @endif">🇲🇲 မြန်မာ</a>

            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary fw-semibold">
                ← {{ __('messages.back_to_products') }}
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm p-3 mb-3">
                <input type="text" id="posSearch" class="form-control py-2" placeholder="{{ __('messages.search_placeholder') }}">
            </div>

            <div class="row row-cols-1 row-cols-md-3 g-3" id="posProductGrid">
                @foreach($products as $product)
                    <div class="col product-item" data-name="{{ strtolower($product->name_en) }} {{ strtolower($product->name_mm) }}" data-code="{{ strtolower($product->product_code) }}">
                        <div class="card h-100 border-0 shadow-sm product-card p-3 text-center"
                             onclick="addToCart({{ $product->id }}, '{{ $product->display_name }}', {{ $product->selling_price }}, {{ $product->stock_quantity }})">
                            <span class="badge bg-secondary position-absolute top-0 start-0 m-2">{{ $product->product_code }}</span>
                            <div class="mt-3">
                                <h6 class="fw-bold mb-1 text-dark">{{ $product->display_name }}</h6>
                                <p class="text-success fw-bold mb-1">{{ number_format($product->selling_price) }} MMK</p>
                                <small class="text-muted">{{ __('messages.stock') }}: {{ $product->stock_quantity }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100 d-flex flex-column">
                <div class="card-header bg-dark text-white fw-bold py-3">
                    📋 {{ __('messages.current_order') }}
                </div>

                <div class="card-body cart-container p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="ps-3">{{ __('messages.item') }}</th>
                            <th>{{ __('messages.price') }}</th>
                            <th width="120">{{ __('messages.stock') }}</th>
                            <th class="text-end pe-3">Total</th>
                        </tr>
                        </thead>
                        <tbody id="cartTableBody">
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">{{ __('messages.cart_empty') }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white border-top p-4 mt-auto">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">{{ __('messages.total_amount') }}:</h5>
                        <h4 class="fw-bold text-danger mb-0" id="cartTotal">0 MMK</h4>
                    </div>
                    <button class="btn btn-success w-100 py-2.5 fw-bold text-uppercase shadow-sm fs-5" onclick="processCheckout()">
                        📥 {{ __('messages.proceed_checkout') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Global Cart Array
    let cart = [];

    // JS ဘက်တွင် ဘာသာစကား Dynamic သုံးနိုင်ရန် PHP မှတစ်ဆင့် Variable ပြောင်းထားခြင်း
    const messages = {
        emptyCart: "{{ __('messages.cart_empty') }}",
        stockOutError: "{{ __('messages.stock_out_error') }}",
        outOfStock: "{{ __('messages.out_of_stock') }}"
    };

    /**
     * ခြင်းတောင်းထဲသို့ ပစ္စည်းထည့်သွင်းခြင်း
     */
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

    /**
     * အရေအတွက် တိုး/လျှော့ လုပ်ခြင်း
     */
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

    /**
     * HTML UI ပေါ်တွင် Cart List အား ပြန်လည်ရေးဆွဲပြသခြင်း
     */
    function renderCart() {
        const tbody = document.getElementById('cartTableBody');
        const totalEl = document.getElementById('cartTotal');

        if (cart.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-5">${messages.emptyCart}</td></tr>`;
            totalEl.innerText = "0 MMK";
            return;
        }

        let html = '';
        let grandTotal = 0;

        cart.forEach(item => {
            let itemTotal = item.price * item.qty;
            grandTotal += itemTotal;

            html += `
            <tr>
                <td class="ps-3 fw-semibold text-dark">${item.name}</td>
                <td class="text-muted">${item.price.toLocaleString()}</td>
                <td>
                    <div class="input-group input-group-sm">
                        <button class="btn btn-outline-secondary" onclick="updateQty(${item.id}, -1)">-</button>
                        <span class="input-group-text px-2.5 fw-bold">${item.qty}</span>
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

    /**
     * Instant Search Filter
     */
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

    /**
     * Checkout လုပ်ဆောင်ချက်
     */
    function processCheckout() {
        if (cart.length === 0) {
            alert(messages.emptyCart);
            return;
        }
        alert("Proceeding to backend with " + cart.length + " items.");
    }
</script>
</body>
</html>
