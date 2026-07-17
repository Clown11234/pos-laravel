@extends('layouts.app')

@section('title', 'POS | Suppliers')
@section('page_title', __('messages.supplier_title') ?? 'Supplier Management')

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
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-truck-field me-2"></i>Supplier & Vendor Records</h5>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-primary w-100 fw-semibold" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                        <i class="fa-solid fa-plus me-1"></i> Add Supplier
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
                        <th>Supplier Name</th>
                        <th>Contact Person</th>
                        <th>Phone Number</th>
                        <th>Address</th>
                        <th>Due Amount (အကြွေးကျန်)</th>
                        <th class="text-center pe-4">Actions</th>
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
                            <td class="text-muted">{{ Str::limit($supplier->address, 40) ?? '-' }}</td>
                            <td>
                                    <span class="fw-bold {{ $supplier->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($supplier->due_amount) }} MMK
                                    </span>
                            </td>
                            <td class="text-center pe-4">
                                <button type="button" class="btn btn-sm btn-outline-warning px-3 me-1 edit-btn" data-id="{{ $supplier->id }}">
                                    Edit
                                </button>

                                <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete this supplier?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger px-3">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                No suppliers registered yet.
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
                        <h5 class="modal-title fw-bold">Add New Supplier</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Supplier/Company Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. U Hla Aye Wholesaler" required>
                            <div class="text-danger fw-semibold error-text name_error mt-1"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Contact Person</label>
                            <input type="text" name="contact_person" class="form-control" placeholder="e.g. Daw Aye Aye (Sales Manager)">
                            <div class="text-danger fw-semibold error-text contact_person_error mt-1"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="e.g. 09123456789" required>
                            <div class="text-danger fw-semibold error-text phone_error mt-1"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Opening Balance / Due Amount</label>
                            <input type="number" name="due_amount" class="form-control" value="0">
                            <div class="text-danger fw-semibold error-text due_amount_error mt-1"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Office Address</label>
                            <textarea name="address" class="form-control" rows="3" placeholder="Enter complete address..."></textarea>
                            <div class="text-danger fw-semibold error-text address_error mt-1"></div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success px-4">Save Supplier</button>
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
                        <h5 class="modal-title fw-bold">Edit Supplier Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Supplier/Company Name</label>
                            <input type="text" id="edit_name" name="name" class="form-control" required>
                            <div class="text-danger fw-semibold error-text edit_name_error mt-1"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Contact Person</label>
                            <input type="text" id="edit_contact_person" name="contact_person" class="form-control">
                            <div class="text-danger fw-semibold error-text edit_contact_person_error mt-1"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" id="edit_phone" name="phone" class="form-control" required>
                            <div class="text-danger fw-semibold error-text edit_phone_error mt-1"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Current Due Amount (MMK)</label>
                            <input type="number" id="edit_due_amount" name="due_amount" class="form-control" required>
                            <div class="text-danger fw-semibold error-text edit_due_amount_error mt-1"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Office Address</label>
                            <textarea id="edit_address" name="address" class="form-control" rows="3"></textarea>
                            <div class="text-danger fw-semibold error-text edit_address_error mt-1"></div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning fw-bold">Update Supplier</button>
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

            // 💡 Laravel Route Helper ကိုသုံးပြီး Dynamic URL ယူလိုက်သဖြင့် Prefix မှားယွင်းမှု လုံးဝမရှိတော့ပါ
            fetch("{{ route('suppliers.store') }}", {
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

                // 💡 Edit URL ကိုလည်း Laravel Dynamic Route စနစ်ဖြင့် ပြောင်းလဲထားပါသည်
                let editUrl = "{{ route('suppliers.edit', ':id') }}".replace(':id', id);

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

            // 💡 Update URL ကိုလည်း Laravel Dynamic Route စနစ်ဖြင့် ကွက်တိပြောင်းလဲထားပါသည်
            let updateUrl = "{{ route('suppliers.update', ':id') }}".replace(':id', id);

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
