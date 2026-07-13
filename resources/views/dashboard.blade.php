@extends('layouts.app')

@section('title', __('POS | Dashboard'))
@section('page_title', __('messages.dashboard_title'))

@section('content')
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3 border-start border-success border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold small mb-1">
                            {{ __('messages.total_revenue') }}
                        </h6>
                        <h3 class="fw-bold text-success mb-0">{{ number_format($totalRevenue) }} MMK</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success fs-3">
                        <i class="fa-solid fa-money-bill-trend-up"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3 border-start border-primary border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold small mb-1">
                            {{ __('messages.total_invoice') }}
                        </h6>
                        <h3 class="fw-bold text-primary mb-0">
                            {{ number_format($totalOrders) }} {{ __('messages.invoice_unit') }}
                        </h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary fs-3">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-danger">
                <i class="fa-solid fa-triangle-exclamation me-2 text-danger"></i>
                {{ __('messages.low_stock_title') }}
            </h5>
            <span class="badge bg-danger rounded-pill fw-bold">
                {{ $lowStockProducts->count() }} {{ __('messages.items_count') }}
            </span>
        </div>
        <div class="card-body p-0">

            @if($lowStockProducts->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-circle-check text-success fs-1 d-block mb-3"></i>
                    <p class="mb-0 fw-semibold">
                        {{ __('messages.all_well_stocked') }}
                    </p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="ps-4">{{ __('messages.product_code') }}</th>
                            <th>{{ __('messages.product_name') }}</th>
                            <th class="text-center">{{ __('messages.alert_threshold') }}</th>
                            <th class="text-end pe-4">{{ __('messages.current_stock') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($lowStockProducts as $product)
                            <tr class="table-danger table-opacity-25">
                                <td class="ps-4 fw-semibold text-secondary">
                                    <i class="fa-solid fa-barcode me-2"></i>{{ $product->product_code }}
                                </td>
                                <td class="fw-bold text-dark">
                                    {{ app()->getLocale() == 'mm' ? $product->name_mm : $product->name_en }}
                                </td>
                                <td class="text-center text-muted fw-semibold">
                                    {{ $product->alert_quantity }} {{ __('messages.pcs') }}
                                </td>
                                <td class="text-end pe-4 text-danger fw-bold fs-5">
                                    <i class="fa-solid fa-arrow-down-short-wide me-1"></i>
                                    {{ $product->stock_quantity }} {{ __('messages.pcs_left') }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>
@endsection
