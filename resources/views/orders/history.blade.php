@extends('layouts.app')

@section('title', __('messages.pos_sales_history'))
@section('page_title', __('messages.history_title'))

@section('content')
    <div class="container-fluid">
        <!-- Search Bar & Title Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>{{ __('messages.sales_history_log') }}
            </h5>

            <!-- Search Form -->
            <form action="{{ route('sales.history') }}" method="GET" class="d-flex gap-2 w-100 w-md-auto" style="max-width: 400px;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                           placeholder="{{ __('messages.search_placeholder') }}"
                           value="{{ request('search') }}">
                    @if(request('search'))
                        <a href="{{ route('sales.history') }}" class="btn btn-outline-secondary d-flex align-items-center">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary fw-bold px-4">{{ __('messages.search') }}</button>
            </form>
        </div>

        <!-- Data Table Card -->
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3">{{ __('messages.invoice_no') }}</th>
                            <th>{{ __('messages.cashier_name') }}</th>
                            <th>{{ __('messages.total_amount') }}</th>
                            <th>{{ __('messages.paid_amount') }}</th>
                            <th>{{ __('messages.change_amount') }}</th>
                            <th>{{ __('messages.discount_amount') }}</th>
                            <th>{{ __('messages.date_and_time') }}</th>
                            <th class="text-center" width="150">{{ __('messages.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">{{ $order->invoice_no ?? '#INV-' . $order->id }}</td>
                                <td class="fw-semibold text-secondary">{{ $order->user->name ?? '-' }}</td>
                                <td class="fw-bold text-success">{{ number_format($order->total_amount) }} {{ __('messages.currency') }}</td>
                                <td class="text-muted">{{ number_format($order->paid_amount) }} {{ __('messages.currency') }}</td>
                                <td class="text-muted">{{ number_format($order->paid_amount - $order->total_amount) }} {{ __('messages.currency') }}</td>
                                <td class="text-muted">{{ number_format($order->discount_amount) }} {{ __('messages.currency') }}</td>
                                <td class="text-secondary small">
                                    {{ $order->created_at->setTimezone(config('app.timezone'))->format('Y-m-d h:i A') }}
                                </td>
                                <td class="text-center">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary fw-bold px-3 view-invoice-btn"
                                            data-id="{{ $order->id }}"
                                            data-url="{{ route('sales.invoice', $order->id) }}">
                                        <i class="fa-solid fa-eye me-1"></i>{{ __('messages.view') }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fs-1 d-block mb-2 text-secondary"></i>
                                    {{ __('messages.error_not_found') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination Links -->
            @if($orders->hasPages() || request('search'))
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Invoice Detail Bootstrap Modal Popup -->
    <div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 400px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom py-2 bg-light">
                    <h6 class="modal-title fw-bold text-dark" id="invoiceModalLabel">
                        <i class="fa-solid fa-file-invoice me-2"></i>{{ __('messages.invoice_detail') }}
                    </h6>
                    <button type="button" class="btn-close custom-close-trigger" aria-label="{{ __('messages.close') }}"></button>
                </div>
                <div class="modal-body bg-white" id="invoiceModalBody">
                    <!-- AJAX Data Loading Status -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary fw-bold custom-close-trigger">{{ __('messages.close') }}</button>
                    <button type="button" class="btn btn-sm btn-success fw-bold px-3" onclick="window.print()">
                        <i class="fa-solid fa-print me-1"></i>{{ __('messages.print_receipt') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const viewButtons = document.querySelectorAll('.view-invoice-btn');
                const invoiceModalElement = document.getElementById('invoiceModal');
                const invoiceModal = new bootstrap.Modal(invoiceModalElement);
                const modalBody = document.getElementById('invoiceModalBody');

                const errorMessage = @json(__('messages.error_loading_invoice'));

                viewButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        const invoiceUrl = this.getAttribute('data-url');
                        modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary spinner-border-sm"></div></div>';
                        invoiceModal.show();

                        fetch(invoiceUrl)
                            .then(response => response.text())
                            .then(html => {
                                modalBody.innerHTML = html;
                            })
                            .catch(error => {
                                modalBody.innerHTML = `<div class="alert alert-danger p-2 small text-center">${errorMessage}</div>`;
                            });
                    });
                });

                const closeTriggers = document.querySelectorAll('.custom-close-trigger');
                closeTriggers.forEach(trigger => {
                    trigger.addEventListener('click', function () {
                        invoiceModal.hide();
                    });
                });

                invoiceModalElement.addEventListener('click', function (e) {
                    if (e.target === invoiceModalElement) {
                        invoiceModal.hide();
                    }
                });
            });
        </script>
    @endpush
@endsection
