@extends('layouts.app')

@section('title', 'Sales History')
@section('page_title', 'အရောင်းသမိုင်းကြောင်းစာရင်း')

@section('content')
    <div class="container-fluid">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-history me-2 text-primary"></i>Sales History Log</h5>
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
                                <td class="text-secondary small">{{ $order->created_at->format('Y-m-d h:i A') }}</td>
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
@endsection
