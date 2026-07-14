@extends('layouts.app')

@section('title', __('POS | Dashboard'))
@section('page_title', __('messages.dashboard_title'))

@section('content')
    <!-- 📊 Summary Stats Cards Section -->
    <div class="row g-4 mb-4">
        <!-- Today's Sales Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3 border-start border-success border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold small mb-1">
                            {{ __('messages.daily_sales') }}
                        </h6>
                        <h3 class="fw-bold text-success mb-0">{{ number_format($todaySales) }} MMK</h3>
                        <small class="text-muted d-block mt-2">{{ __('messages.daily_invoice_count') }}: {{ number_format($todayOrdersCount) }} စောင်</small>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success fs-3">
                        <i class="fa-solid fa-calendar-day"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- This Month's Sales Card -->

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3 border-start border-success border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold small mb-1">
                            {{ __('messages.monthly_revenue') }}
                        </h6>
                        <h3 class="fw-bold text-success mb-0">{{ number_format($monthSales) }} MMK</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success fs-3">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- All-Time Total Revenue Card (Originally col-md-6) -->
        <div class="col-md-4">
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
    </div>

    <div class="row g-4 mb-4">
        <!-- All-Time Total Invoice Card (Originally col-md-6) -->
        <div class="col-12">
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

    <div class="row g-4 mb-4">
        <!-- 🏆 Top Selling Products Table Section -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-trophy me-2 text-warning"></i>{{ __('messages.best_seller_product') }}
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th width="80">{{ __('messages.ranking') }}</th>
                                <th>{{ __('messages.product_name') }}</th>
                                <th class="text-center">{{ __('messages.qty') }}</th>
                                <th class="text-end pe-4">{{ __('messages.product_revenue') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($topProducts as $index => $item)
                                <tr>
                                    <td class="ps-4 fw-bold">
                                        @if($index == 0)
                                            <span class="badge bg-warning text-dark px-2"><i class="fa-solid fa-crown me-1"></i>1st</span>
                                        @elseif($index == 1)
                                            <span class="badge bg-secondary text-white px-2">2nd</span>
                                        @elseif($index == 2)
                                            <span class="badge bg-danger text-white px-2">3rd</span>
                                        @else
                                            <span class="badge bg-light text-dark border px-2">{{ $index + 1 }}th</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 180px;">
                                            {{ app()->getLocale() == 'mm' ? ($item->product->name_mm ?? $item->product->name_en) : $item->product->name_en }}
                                        </div>
                                        <small class="text-muted">SKU: {{ $item->product->sku ?? 'N/A' }}</small>
                                    </td>
                                    <td class="text-center fw-semibold text-primary">{{ number_format($item->total_qty) }} ခု</td>
                                    <td class="text-end fw-bold text-success pe-4">{{ number_format($item->total_revenue) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-chart-pie fs-1 d-block mb-2 text-secondary"></i>
                                        အရောင်းရဆုံး ပစ္စည်းစာရင်း တွက်ချက်ရန် မှတ်တမ်းမရှိသေးပါ။
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ⚠️ (Original Feature) Low Stock Alerts Section -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
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
                        <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light sticky-top">
                                <tr>
                                    <th class="ps-3">{{ __('messages.product_name') }}</th>
                                    <th class="text-end pe-3">{{ __('messages.current_stock') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($lowStockProducts as $product)
                                    <tr class="table-danger table-opacity-25">
                                        <td class="ps-3">
                                            <div class="fw-bold text-dark text-truncate" style="max-width: 150px;">
                                                {{ app()->getLocale() == 'mm' ? $product->name_mm : $product->name_en }}
                                            </div>
                                            <small class="text-muted"><i class="fa-solid fa-barcode me-1"></i>{{ $product->product_code }}</small>
                                        </td>
                                        <td class="text-end pe-3 text-danger fw-bold">
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
        </div>
    </div>
@endsection
