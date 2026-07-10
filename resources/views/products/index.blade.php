<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.products') ?? 'Products Management' }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .error-text { font-size: 0.85rem; margin-top: 0.25rem; }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark mb-0">{{ __('messages.products') ?? 'Products Management' }}</h2>
        <div class="d-flex align-items-center">

            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary px-4 fw-semibold shadow-sm me-2">
                <i class="fa-solid fa-chart-line me-1"></i> Go to Dashboard
            </a>
            <a href="{{ route('products.pos') }}" class="btn btn-success px-4 fw-semibold shadow-sm me-2">
                Go to POS Counter
            </a>

            <button type="button" class="btn btn-primary px-4 fw-semibold shadow-sm me-2" data-bs-toggle="modal" data-bs-target="#addProductModal">
                + Add Product
            </button>

            <a href="{{ route('lang.switch', 'en') }}" class="btn btn-sm btn-outline-primary ms-1 @if(app()->getLocale() == 'en') active @endif">🇬🇧 English</a>
            <a href="{{ route('lang.switch', 'mm') }}" class="btn btn-sm btn-outline-primary ms-1 me-3 @if(app()->getLocale() == 'mm') active @endif">🇲🇲 မြန်မာ</a>

            <div class="d-flex align-items-center bg-white px-3 py-1.5 rounded-3 shadow-sm border">
                <span class="text-dark fw-bold me-3"> {{ auth()->user()->name }} ({{ strtoupper(auth()->user()->role) }})</span>

                <form action="{{ route('logout') }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to logout?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger fw-semibold px-3 py-1.5 rounded-3 shadow-sm">
                        {{ app()->getLocale() == 'mm' ? 'အကောင့်ထွက်ရန်' : 'Logout' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="alertBox" class="alert d-none shadow-sm" role="alert"></div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('products.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by name or code...">
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Categories (အုပ်စုအားလုံး)</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @if(request('category_id') == $category->id) selected @endif>
                                {{ $category->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-dark w-100 fw-semibold">Filter Search</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark text-uppercase fs-7">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>{{ __('messages.code') ?? 'Code' }}</th>
                        <th>{{ __('messages.products') ?? 'Product Name' }}</th>
                        <th>Category</th>
                        <th>{{ __('messages.price') ?? 'Selling Price' }}</th>
                        <th>{{ __('messages.stock') ?? 'Stock' }}</th>
                        <th class="text-center pe-4">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="ps-4 fw-semibold text-secondary">
                                {{ $loop->iteration + ($products->firstItem() - 1) }}
                            </td>
                            <td>
                                <span class="badge bg-secondary px-2.5 py-1.5">{{ $product->product_code }}</span>
                            </td>
                            <td class="fw-semibold text-dark">{{ $product->display_name }}</td>
                            <td><span class="text-muted">{{ $product->category->display_name ?? 'N/A' }}</span></td>
                            <td class="fw-bold text-success">{{ number_format($product->selling_price) }} MMK</td>
                            <td>
                                <span class="fw-semibold @if($product->stock_quantity <= $product->alert_quantity) text-danger @else text-dark @endif">
                                    {{ $product->stock_quantity }}
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <button type="button" class="btn btn-sm btn-outline-warning px-3 me-1 edit-btn" data-id="{{ $product->id }}">
                                    Edit
                                </button>

                                <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                      onsubmit="return confirm('Are you sure you want to move this product to trash?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger px-3">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                No products found matching your search criteria.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($products->hasPages())
            <div class="card-footer bg-white pt-3 border-0 d-flex justify-content-center">
                {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="productForm">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold">Add New POS Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Product Category (ကုန်ပစ္စည်းအုပ်စု)</label>
                        <select name="category_id" class="form-select" required>
                            <option value="" selected disabled>-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name_en }} ({{ $category->name_mm }})</option>
                            @endforeach
                        </select>
                        <div class="text-danger fw-semibold error-text category_id_error"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Product Code / Barcode</label>
                        <input type="text" name="product_code" class="form-control" placeholder="e.g. PROD-1001" required>
                        <div class="text-danger fw-semibold error-text product_code_error"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Product Name (English)</label>
                        <input type="text" name="name_en" class="form-control" placeholder="e.g. Coca Cola" required>
                        <div class="text-danger fw-semibold error-text name_en_error"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Product Name (Myanmar)</label>
                        <input type="text" name="name_mm" class="form-control" placeholder="ဥပမာ - ကိုကာကိုလာ" required>
                        <div class="text-danger fw-semibold error-text name_mm_error"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Cost Price (ရင်းစျေး)</label>
                            <input type="number" name="cost_price" class="form-control" placeholder="0" required>
                            <div class="text-danger fw-semibold error-text cost_price_error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Selling Price (ရောင်းစျေး)</label>
                            <input type="number" name="selling_price" class="form-control" placeholder="0" required>
                            <div class="text-danger fw-semibold error-text selling_price_error"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Stock Quantity</label>
                            <input type="number" name="stock_quantity" class="form-control" value="10" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Alert Quantity</label>
                            <input type="number" name="alert_quantity" class="form-control" value="5" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="editProductForm">
                <input type="hidden" id="edit_product_id">

                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Product Category</label>
                        <select id="edit_category_id" name="category_id" class="form-select" required>
                            <option value="" disabled>-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name_en }}</option>
                            @endforeach
                        </select>
                        <div class="text-danger error-text edit_category_id_error"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Product Code</label>
                        <input type="text" id="edit_product_code" name="product_code" class="form-control" required>
                        <div class="text-danger error-text edit_product_code_error"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name (EN)</label>
                        <input type="text" id="edit_name_en" name="name_en" class="form-control" required>
                        <div class="text-danger error-text edit_name_en_error"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name (MM)</label>
                        <input type="text" id="edit_name_mm" name="name_mm" class="form-control" required>
                        <div class="text-danger error-text edit_name_mm_error"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Cost Price</label>
                            <input type="number" id="edit_cost_price" name="cost_price" class="form-control" required>
                            <div class="text-danger error-text edit_cost_price_error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Selling Price</label>
                            <input type="number" id="edit_selling_price" name="selling_price" class="form-control" required>
                            <div class="text-danger error-text edit_selling_price_error"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Stock Qty</label>
                            <input type="number" id="edit_stock_quantity" name="stock_quantity" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Alert Qty</label>
                            <input type="number" id="edit_alert_quantity" name="alert_quantity" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning fw-bold">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // ==================== [AJAX CREATE] ====================
    document.getElementById('productForm').addEventListener('submit', function(e) {
        e.preventDefault();
        document.querySelectorAll('.error-text').forEach(el => el.innerText = '');
        fetch("{{ route('products.store') }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": csrfToken, "Accept": "application/json" },
            body: new FormData(this)
        })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(res => {
                if (res.status === 201) {
                    bootstrap.Modal.getInstance(document.getElementById('addProductModal')).hide();
                    showAlert("Product saved successfully!", "success");
                    document.getElementById('productForm').reset();
                    setTimeout(() => location.reload(), 1200);
                } else if (res.status === 422) {
                    for (const key in res.body.errors) {
                        document.querySelector(`.${key}_error`).innerText = res.body.errors[key][0];
                    }
                }
            });
    });

    // ==================== [AJAX EDIT - FETCH DATA] ====================
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');

            fetch(`/products/${id}/edit`, {
                method: "GET",
                headers: { "Accept": "application/json" }
            })
                .then(res => res.json())
                .then(product => {
                    document.getElementById('edit_product_id').value = product.id;
                    document.getElementById('edit_product_code').value = product.product_code;
                    document.getElementById('edit_name_en').value = product.name_en;
                    document.getElementById('edit_name_mm').value = product.name_mm;
                    document.getElementById('edit_cost_price').value = product.cost_price;
                    document.getElementById('edit_selling_price').value = product.selling_price;
                    document.getElementById('edit_stock_quantity').value = product.stock_quantity;
                    document.getElementById('edit_alert_quantity').value = product.alert_quantity;
                    document.getElementById('edit_category_id').value = product.category_id;

                    new bootstrap.Modal(document.getElementById('editProductModal')).show();
                });
        });
    });

    // ==================== [AJAX UPDATE - SAVE CHANGES] ====================
    document.getElementById('editProductForm').addEventListener('submit', function(e) {
        e.preventDefault();
        document.querySelectorAll('.error-text').forEach(el => el.innerText = '');

        const id = document.getElementById('edit_product_id').value;
        const formData = new FormData(this);
        formData.append('_method', 'PUT');

        fetch(`/products/${id}`, {
            method: "POST",
            headers: { "X-CSRF-TOKEN": csrfToken, "Accept": "application/json" },
            body: formData
        })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(res => {
                if (res.status === 200) {
                    bootstrap.Modal.getInstance(document.getElementById('editProductModal')).hide();
                    showAlert(res.body.message, "success");
                    setTimeout(() => location.reload(), 1200);
                } else if (res.status === 422) {
                    for (const key in res.body.errors) {
                        document.querySelector(`.edit_${key}_error`).innerText = res.body.errors[key][0];
                    }
                }
            });
    });

    function showAlert(message, type) {
        const alertBox = document.getElementById('alertBox');
        alertBox.className = `alert alert-${type} shadow-sm d-block`;
        alertBox.innerText = message;
    }
</script>
</body>
</html>
