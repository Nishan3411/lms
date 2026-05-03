<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">Payment Receipt</h2>
                <p class="module-copy">Printable receipt for {{ $payment->studentFee->student->name }}.</p>
            </div>
            <button onclick="window.print()" class="secondary-action">Print Receipt</button>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell max-w-3xl">
            <div class="module-card">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 pb-6">
                    <div>
                        <h3 class="text-2xl font-semibold text-slate-900">LMS Payment Receipt</h3>
                        <p class="mt-1 text-sm text-slate-500">Receipt #PAY-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <span class="data-pill">{{ $payment->paid_at->format('d M Y') }}</span>
                </div>

                <div class="mt-6 space-y-3 text-sm">
                    <div class="module-subcard flex justify-between gap-4">
                        <span class="text-slate-500">Student</span>
                        <span class="font-medium text-slate-900">{{ $payment->studentFee->student->name }}</span>
                    </div>
                    <div class="module-subcard flex justify-between gap-4">
                        <span class="text-slate-500">Class</span>
                        <span class="font-medium text-slate-900">{{ $payment->studentFee->fee->classRoom->name }}</span>
                    </div>
                    <div class="module-subcard flex justify-between gap-4">
                        <span class="text-slate-500">Fee</span>
                        <span class="font-medium text-slate-900">{{ $payment->studentFee->fee->title }}</span>
                    </div>
                    <div class="module-subcard flex justify-between gap-4">
                        <span class="text-slate-500">Amount</span>
                        <span class="font-medium text-slate-900">{{ number_format($payment->amount, 2) }}</span>
                    </div>
                    <div class="module-subcard flex justify-between gap-4">
                        <span class="text-slate-500">Method</span>
                        <span class="font-medium text-slate-900">{{ $payment->payment_method }}</span>
                    </div>
                    <div class="module-subcard flex justify-between gap-4">
                        <span class="text-slate-500">Transaction ID</span>
                        <span class="font-medium text-slate-900">{{ $payment->transaction_id ?? 'N/A' }}</span>
                    </div>
                    <div class="module-subcard flex justify-between gap-4">
                        <span class="text-slate-500">Paid At</span>
                        <span class="font-medium text-slate-900">{{ $payment->paid_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
