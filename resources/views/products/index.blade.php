@extends('layouts.app')

@section('title', __('messages.product_list'))
@section('page_title', __('messages.product_list'))

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Filter & Search Form --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('products.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="{{ __('messages.search_placeholder') }}">
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select" onchange="this.form.submit()">
                        <option value="">{{ __('messages.all_categories') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-primary w-100 fw-semibold" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fa-solid fa-plus me-1"></i> {{ __('messages.add_product') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Product Table Section --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark text-uppercase fs-7">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>{{ __('messages.code') }}</th>
                        <th>{{ __('messages.product_name') }}</th>
                        <th>{{ __('messages.categories') }}</th>
                        <th>{{ __('messages.price') }}</th>
                        <th>{{ __('messages.stock') }}</th>
                        <th class="text-center pe-4">{{ __('messages.actions') }}</th>
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

                            <td class="fw-semibold text-dark">{{ $product->name }}</td>
                            <td><span class="text-muted">{{ $product->category->name ?? '-' }}</span></td>

                            <td class="fw-bold text-success">{{ number_format($product->selling_price) }} {{ __('messages.currency') }}</td>

                            <td>
                                <span class="{{ $product->stock_class }}">
                                    {{ $product->stock_quantity }}
                                </span>
                            </td>

                            <td class="text-center pe-4">
                                <button type="button" class="btn btn-sm btn-outline-warning px-3 me-1 edit-btn" data-id="{{ $product->id }}">
                                    {{ __('messages.edit') }}
                                </button>

                                <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                      onsubmit="return confirm('{{ __('messages.confirm_delete') }}');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger px-3">
                                        {{ __('messages.delete') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                {{ __('messages.no_products') }}
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

    {{-- Add Product Modal --}}
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="productForm">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title fw-bold">{{ __('messages.add_product') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('messages.categories') }}</label>
                            <select name="category_id" class="form-select" required>
                                <option value="" selected disabled>{{ __('messages.select_category') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger fw-semibold error-text category_id_error"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('messages.code') }}</label>
                            <input type="text" name="product_code" class="form-control" placeholder="{{ __('messages.code_placeholder') }}" required>
                            <div class="text-danger fw-semibold error-text product_code_error"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('messages.product_name_en') }}</label>
                            <input type="text" name="name_en" class="form-control" required>
                            <div class="text-danger fw-semibold error-text name_en_error"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('messages.product_name_mm') }}</label>
                            <input type="text" name="name_mm" class="form-control">
                            <div class="text-danger fw-semibold error-text name_mm_error"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">{{ __('messages.cost_price') }}</label>
                                <input type="number" name="cost_price" class="form-control" required>
                                <div class="text-danger fw-semibold error-text cost_price_error"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">{{ __('messages.price') }}</label>
                                <input type="number" name="selling_price" class="form-control" required>
                                <div class="text-danger fw-semibold error-text selling_price_error"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">{{ __('messages.stock_quantity') }}</label>
                                <input type="number" name="stock_quantity" class="form-control" value="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">{{ __('messages.alert_quantity') }}</label>
                                <input type="number" name="alert_quantity" class="form-control" value="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-success px-4">{{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Product Modal --}}
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="editProductForm">
                    <input type="hidden" id="edit_product_id">

                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title fw-bold">{{ __('messages.edit_product') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('messages.categories') }}</label>
                            <select id="edit_category_id" name="category_id" class="form-select" required>
                                <option value="" disabled>{{ __('messages.select_category') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger error-text edit_category_id_error"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('messages.code') }}</label>
                            <input type="text" id="edit_product_code" name="product_code" class="form-control" required>
                            <div class="text-danger error-text edit_product_code_error"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('messages.product_name_en') }}</label>
                            <input type="text" id="edit_name_en" name="name_en" class="form-control" required>
                            <div class="text-danger error-text edit_name_en_error"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('messages.product_name_mm') }}</label>
                            <input type="text" id="edit_name_mm" name="name_mm" class="form-control">
                            <div class="text-danger error-text edit_name_mm_error"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">{{ __('messages.cost_price') }}</label>
                                <input type="number" id="edit_cost_price" name="cost_price" class="form-control" required>
                                <div class="text-danger error-text edit_cost_price_error"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">{{ __('messages.price') }}</label>
                                <input type="number" id="edit_selling_price" name="selling_price" class="form-control" required>
                                <div class="text-danger error-text edit_selling_price_error"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">{{ __('messages.stock_quantity') }}</label>
                                <input type="number" id="edit_stock_quantity" name="stock_quantity" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">{{ __('messages.alert_quantity') }}</label>
                                <input type="number" id="edit_alert_quantity" name="alert_quantity" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-warning fw-bold">{{ __('messages.update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            // Add Product Form
            const productForm = document.getElementById('productForm');
            if (productForm) {
                productForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    document.querySelectorAll('.error-text').forEach(el => el.innerText = '');

                    fetch("{{ route('products.store') }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": csrfToken,
                            "Accept": "application/json"
                        },
                        body: new FormData(this)
                    })
                        .then(response => response.json().then(data => ({ status: response.status, body: data })))
                        .then(res => {
                            if (res.status === 201) {
                                const modalEl = document.getElementById('addProductModal');
                                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                                modal.hide();
                                location.reload();
                            } else if (res.status === 422) {
                                for (const key in res.body.errors) {
                                    const errorEl = document.querySelector(`.${key}_error`);
                                    if (errorEl) errorEl.innerText = res.body.errors[key][0];
                                }
                            }
                        })
                        .catch(err => console.error("Create Product Error:", err));
                });
            }

            // Edit
            document.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('edit-btn')) {
                    const button = e.target;
                    const id = button.getAttribute('data-id');
                    const editUrl = "{{ route('products.edit', ':id') }}".replace(':id', id);

                    fetch(editUrl, {
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

                            const editModalEl = document.getElementById('editProductModal');
                            const editModal = bootstrap.Modal.getOrCreateInstance(editModalEl);
                            editModal.show();
                        })
                        .catch(err => console.error("Fetch Product Error:", err));
                }
            });

            //  Update Product
            const editProductForm = document.getElementById('editProductForm');
            if (editProductForm) {
                editProductForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    document.querySelectorAll('.error-text').forEach(el => el.innerText = '');

                    const id = document.getElementById('edit_product_id').value;
                    const updateUrl = "{{ route('products.update', ':id') }}".replace(':id', id);

                    const formData = new FormData(this);
                    formData.append('_method', 'PUT');

                    fetch(updateUrl, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": csrfToken,
                            "Accept": "application/json"
                        },
                        body: formData
                    })
                        .then(response => response.json().then(data => ({ status: response.status, body: data })))
                        .then(res => {
                            if (res.status === 200) {
                                const editModalEl = document.getElementById('editProductModal');
                                const editModal = bootstrap.Modal.getInstance(editModalEl) || new bootstrap.Modal(editModalEl);
                                editModal.hide();
                                location.reload();
                            } else if (res.status === 422) {
                                for (const key in res.body.errors) {
                                    const errorEl = document.querySelector(`.edit_${key}_error`);
                                    if (errorEl) errorEl.innerText = res.body.errors[key][0];
                                }
                            }
                        })
                        .catch(err => console.error("Update Product Error:", err));
                });
            }
        });
    </script>
@endpush
