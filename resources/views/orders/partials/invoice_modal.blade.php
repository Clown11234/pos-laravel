<!-- resources/views/orders/partials/invoice_modal.blade.php -->
<div class="invoice-print-area p-2 text-dark" id="printableInvoice">
    <!-- ဆိုင်အမည်နှင့် ခေါင်းစဉ် -->
    <div class="text-center mb-3">
        <h4 class="fw-bold mb-1">56 POS STORE</h4>
        <p class="small text-muted mb-0">Yangon, Myanmar<br>Tel: 09-978882960</p>
        <hr class="border-secondary my-2">
        <h6 class="fw-bold text-uppercase">SALES RECEIPT</h6>
    </div>

    <!-- ပြေစာ အချက်အလက်များ -->
    <div class="row g-2 small mb-3">
        <div class="col-6"><strong>Inv No:</strong> #INV-{{ $order->id }}</div>
        <div class="col-6 text-end"><strong>Date:</strong> {{ $order->created_at->setTimezone('Asia/Yangon')->format('Y-m-d h:i A') }}</div>
        <div class="col-12"><strong>Cashier:</strong> {{ $order->user->name }}</div>
    </div>

    <!-- ဝယ်ယူခဲ့သည့် ပစ္စည်းစာရင်း ဇယား -->
    <table class="table table-sm table-borderless small w-100 mb-3 align-middle">
        <thead class="border-bottom border-dark">
        <tr>
            <th>{{ __('messages.item') }}</th>
            <th class="text-center">{{ __('messages.qty') }}</th>
            <th class="text-end">{{ __('messages.price') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->orderItems as $item)
            <tr class="border-bottom border-light">
                <td>
                    {{ app()->getLocale() == 'mm' ? $item->product->name_mm : $item->product->name_en }}
                </td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-end">{{ number_format($item->price * $item->quantity) }} MMK</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <!-- တွက်ချက်မှု အနှစ်ချုပ် အပိုင်း -->
    <div class="row g-2 small border-top border-dark pt-2 fw-bold">
        <div class="col-7 text-uppercase">{{ __('messages.total_amount') }}:</div>
        <div class="col-5 text-end text-success">{{ number_format($order->total_amount) }} MMK</div>

        <div class="col-7 text-muted fw-normal">{{ __('messages.paid_amount') }}:</div>
        <div class="col-5 text-end text-muted fw-normal">{{ number_format($order->paid_amount) }} MMK</div>

        <div class="col-7 text-muted fw-normal">{{ __('messages.change_amount') }}:</div>
        <div class="col-5 text-end text-muted fw-normal">{{ number_format($order->paid_amount - $order->total_amount) }} MMK</div>
    </div>

    <div class="text-center mt-4 small border-top pt-2">
        <p class="mb-0 text-muted fw-semibold">{{ __('messages.thank_you') }}</p>
    </div>
</div>

<!--  Thermal Printer Layout အတွက် သီးသန့် Print CSS Style -->
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printableInvoice, #printableInvoice * {
            visibility: visible;
        }
        #printableInvoice {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            max-width: 80mm; /* Thermal Paper Standard Size */
            font-family: 'Courier New', Courier, monospace;
            padding: 0;
            margin: 0;
        }
        .btn, .modal-header, .modal-footer {
            display: none !important;
        }
    }
</style>
