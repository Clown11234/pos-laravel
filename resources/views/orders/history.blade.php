@extends('layouts.app')

@section('title', 'POS | Sales History')
@section('page_title', __('messages.history_title'))

@section('content')
    <div class="container-fluid">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Sales History Log
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3">Invoice No</th>
                            <th>{{ __('messages.cashier_name') }}</th>
                            <th>{{ __('messages.total_amount') }}</th>
                            <th>{{ __('messages.paid_amount') }}</th>
                            <th>{{ __('messages.change_amount') }}</th>
                            <th>Date & Time</th>
                            <th class="text-center" width="150">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">#INV-{{ $order->id }}</td>
                                <td class="fw-semibold text-secondary">{{ $order->user->name }}</td>
                                <td class="fw-bold text-success">{{ number_format($order->total_amount) }} MMK</td>
                                <td class="text-muted">{{ number_format($order->paid_amount) }} MMK</td>
                                <td class="text-muted">{{ number_format($order->paid_amount - $order->total_amount) }} MMK</td>
                                <td class="text-secondary small">
                                    {{ $order->created_at->setTimezone('Asia/Yangon')->format('Y-m-d h:i A') }}
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary fw-bold px-3 view-invoice-btn" data-id="{{ $order->id }}">
                                        <i class="fa-solid fa-eye me-1"></i>View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fs-1 d-block mb-2 text-secondary"></i>
                                    ယခုထိ အရောင်းမှတ်တမ်း မရှိသေးပါ။
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($orders->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- 📦 Invoice Detail Bootstrap Modal Popup -->
    <div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 400px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom py-2 bg-light">
                    <h6 class="modal-title fw-bold text-dark" id="invoiceModalLabel">
                        <i class="fa-solid fa-file-invoice me-2"></i>Invoice Detail
                    </h6>
                    <!--  အပေါ်က ကန့်လန့်ဖြတ် ပိတ်သည့်ခလုတ် -->
                    <button type="button" class="btn-close custom-close-trigger" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-white" id="invoiceModalBody">
                    <!-- AJAX Data Loading Status -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 bg-light d-flex justify-content-between">
                    <!-- 🚪 အောက်ခြေက Close ပိတ်သည့်ခလုတ် -->
                    <button type="button" class="btn btn-sm btn-secondary fw-bold custom-close-trigger">Close</button>
                    <button type="button" class="btn btn-sm btn-success fw-bold px-3" onclick="window.print()">
                        <i class="fa-solid fa-print me-1"></i>Print Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ⚡ AJAX နှင့် မော်ဒယ်ဖွင့်/ပိတ်လုပ်မည့် Pure JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const viewButtons = document.querySelectorAll('.view-invoice-btn');
            const invoiceModalElement = document.getElementById('invoiceModal');
            const invoiceModal = new bootstrap.Modal(invoiceModalElement);
            const modalBody = document.getElementById('invoiceModalBody');

            // ၁။ View ခလုတ်နှိပ်လျှင် ဒေတာဆွဲပြီး Modal ဖွင့်ခြင်း
            viewButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const orderId = this.getAttribute('data-id');
                    modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary spinner-border-sm"></div></div>';
                    invoiceModal.show();

                    fetch(`/sales/invoice/${orderId}`)
                        .then(response => response.text())
                        .then(html => {
                            modalBody.innerHTML = html;
                        })
                        .catch(error => {
                            modalBody.innerHTML = '<div class="alert alert-danger p-2 small text-center">Error loading invoice details.</div>';
                        });
                });
            });

            // ၂။ ပိတ်မရသည့်ပြဿနာကို ဖြေရှင်းရန် - Close ခလုတ်အားလုံးကို လက်လှမ်းပိတ်ခိုင်းခြင်း
            const closeTriggers = document.querySelectorAll('.custom-close-trigger');
            closeTriggers.forEach(trigger => {
                trigger.addEventListener('click', function () {
                    invoiceModal.hide();
                });
            });

            // ၃။ Modal ၏ နောက်ခံမည်းမည်း (Backdrop) ကို နှိပ်လျှင်လည်း ပိတ်ပေးခြင်း
            invoiceModalElement.addEventListener('click', function (e) {
                if (e.target === invoiceModalElement) {
                    invoiceModal.hide();
                }
            });
        });
    </script>
@endsection
