@extends('layouts.app')

@section('title', 'POS | ' . __('messages.supplier_title'))
@section('page_title', __('messages.supplier_title'))

@section('content')
    {{-- Notification Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Top Action Bar --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row align-items-center g-3">
                <div class="col-md-9">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-truck-field me-2"></i>{{ __('messages.supplier_records') }}</h5>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-primary w-100 fw-semibold" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                        <i class="fa-solid fa-plus me-1"></i> {{ __('messages.add_supplier') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Suppliers Table Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark text-uppercase fs-7">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>{{ __('messages.supplier_name') }}</th>
                        <th>{{ __('messages.contact_person') }}</th>
                        <th>{{ __('messages.phone_number') }}</th>
                        <th>{{ __('messages.address') }}</th>
                        <th>{{ __('messages.due_amount') }}</th>
                        <th class="text-center pe-4">{{ __('messages.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td class="ps-4 fw-semibold text-secondary">
                                {{ $loop->iteration + ($suppliers->firstItem() - 1) }}
                            </td>
                            <td class="fw-bold text-dark">{{ $supplier->name }}</td>
                            <td>{{ $supplier->contact_person ?? '-' }}</td>
                            <td><span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="fa-solid fa-phone me-1 text-muted"></i>{{ $supplier->phone }}</span></td>
                            <td class="text-muted">{{ \Illuminate\Support\Str::limit($supplier->address, 40) ?? '-' }}</td>
                            <td>
                                    <span class="fw-bold {{ $supplier->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($supplier->due_amount) }} MMK
                                    </span>
                            </td>
                            <td class="text-center pe-4">
                                <button type="button" class="btn btn-sm btn-outline-warning px-3 me-1 edit-btn" data-id="{{ $supplier->id }}">
                                    {{ __('messages.edit') }}
                                </button>

                                <form action="{{ route('sales.suppliers.destroy', $supplier->id) }}" method="POST"
                                      onsubmit="return confirm('{{ __('messages.confirm_delete_supplier') }}');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger px-3">{{ __('messages.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                {{ __('messages.no_suppliers') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination Links --}}
        @if($suppliers->hasPages())
            <div class="card-footer bg-white pt-3 border-0 d-flex justify-content-center">
                {{ $suppliers->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    {{-- Add Supplier Modal --}}
    <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="supplierForm">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title fw-bold">{{ __('messages.add_new_supplier') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('messages.supplier_company_name') }}</label>
                            <input type="text" name="name" class="form-control" placeholder="{{ __('messages.supplier_name_placeholder') }}" required>
                            <div class="text-danger fw-semibold error-text name_error mt-1"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('messages.contact_person') }}</label>
                            <input type="text" name="contact_person" class="form-control" placeholder="{{ __('messages.contact_person_placeholder') }}">
                            <div class="text-danger fw-semibold error-text contact_person_error mt-1"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('messages.phone_number') }}</label>
                            <input type="text" name="phone" class="form-control" placeholder="{{ __('messages.phone_placeholder') }}" required>
                            <div class="text-danger fw-semibold error-text phone_error mt-1"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('messages.opening_balance') }}</label>
                            <input type="number" name="due_amount" class="form-control" value="0">
                            <div class="text-danger fw-semibold error-text due_amount_error mt-1"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('messages.office_address') }}</label>
                            <textarea name="address" class="form-control" rows="3" placeholder="{{ __('messages.address_placeholder') }}"></textarea>
                            <div class="text-danger fw-semibold error-text address_error mt-1"></div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-success px-4">{{ __('messages.save_supplier') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Supplier Modal --}}
    <div class="modal fade" id="editSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="editSupplierForm">
                    <input type="hidden" id="edit_supplier_id">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title fw-bold">{{ __('messages.edit_supplier_details') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('messages.supplier_company_name') }}</label>
                            <input type="text" id="edit_name" name="name" class="form-control" required>
                            <div class="text-danger fw-semibold error-text edit_name_error mt-1"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('messages.contact_person') }}</label>
                            <input type="text" id="edit_contact_person" name="contact_person" class="form-control">
                            <div class="text-danger fw-semibold error-text edit_contact_person_error mt-1"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('messages.phone_number') }}</label>
                            <input type="text" id="edit_phone" name="phone" class="form-control" required>
                            <div class="text-danger fw-semibold error-text edit_phone_error mt-1"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('messages.current_due_amount') }}</label>
                            <input type="number" id="edit_due_amount" name="due_amount" class="form-control" required>
                            <div class="text-danger fw-semibold error-text edit_due_amount_error mt-1"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('messages.office_address') }}</label>
                            <textarea id="edit_address" name="address" class="form-control" rows="3"></textarea>
                            <div class="text-danger fw-semibold error-text edit_address_error mt-1"></div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-warning fw-bold">{{ __('messages.update_supplier') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenElement ? csrfTokenElement.getAttribute('content') : '';

        function clearErrors() {
            document.querySelectorAll('.error-text').forEach(el => el.innerText = '');
        }

        // --- ADD SUPPLIER AJAX ---
        document.getElementById('supplierForm').addEventListener('submit', function(e) {
            e.preventDefault();
            clearErrors();

            fetch("{{ route('sales.suppliers.store') }}", {
                method: "POST",
                headers: { "X-CSRF-TOKEN": csrfToken, "Accept": "application/json" },
                body: new FormData(this)
            })
                .then(response => response.json().then(data => ({ status: response.status, body: data })))
                .then(res => {
                    if (res.status === 201) {
                        bootstrap.Modal.getInstance(document.getElementById('addSupplierModal')).hide();
                        location.reload();
                    } else if (res.status === 422) {
                        for (const key in res.body.errors) {
                            const errBox = document.querySelector(`.${key}_error`);
                            if (errBox) errBox.innerText = res.body.errors[key][0];
                        }
                    }
                })
                .catch(err => console.error("Error adding supplier:", err));
        });

        // --- EDIT BUTTON CLICK (Event Delegation Pattern) ---
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('edit-btn')) {
                const id = e.target.getAttribute('data-id');
                clearErrors();

                let editUrl = "{{ route('sales.suppliers.edit', ':id') }}".replace(':id', id);

                fetch(editUrl, {
                    method: "GET",
                    headers: { "Accept": "application/json" }
                })
                    .then(res => res.json())
                    .then(supplier => {
                        document.getElementById('edit_supplier_id').value = supplier.id;
                        document.getElementById('edit_name').value = supplier.name;
                        document.getElementById('edit_contact_person').value = supplier.contact_person || '';
                        document.getElementById('edit_phone').value = supplier.phone;
                        document.getElementById('edit_due_amount').value = supplier.due_amount;
                        document.getElementById('edit_address').value = supplier.address || '';

                        new bootstrap.Modal(document.getElementById('editSupplierModal')).show();
                    })
                    .catch(err => console.error("Error fetching supplier details:", err));
            }
        });

        // --- UPDATE SUPPLIER AJAX ---
        document.getElementById('editSupplierForm').addEventListener('submit', function(e) {
            e.preventDefault();
            clearErrors();

            const id = document.getElementById('edit_supplier_id').value;
            const formData = new FormData(this);
            formData.append('_method', 'PUT');

            let updateUrl = "{{ route('sales.suppliers.update', ':id') }}".replace(':id', id);

            fetch(updateUrl, {
                method: "POST",
                headers: { "X-CSRF-TOKEN": csrfToken, "Accept": "application/json" },
                body: formData
            })
                .then(response => response.json().then(data => ({ status: response.status, body: data })))
                .then(res => {
                    if (res.status === 200) {
                        bootstrap.Modal.getInstance(document.getElementById('editSupplierModal')).hide();
                        location.reload();
                    } else if (res.status === 422) {
                        for (const key in res.body.errors) {
                            const errBox = document.querySelector(`.edit_${key}_error`);
                            if (errBox) errBox.innerText = res.body.errors[key][0];
                        }
                    }
                })
                .catch(err => console.error("Error updating supplier:", err));
        });

        // Reset Add Form on Modal hidden
        document.getElementById('addSupplierModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('supplierForm').reset();
            clearErrors();
        });
    </script>
@endpush
