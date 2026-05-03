<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">Fee Invoice</h2>
                <p class="module-copy">Printable fee invoice for {{ $studentFee->student->name }}.</p>
            </div>
            <button onclick="window.print()" class="secondary-action">Print Invoice</button>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell max-w-4xl">
            <div class="module-card">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 pb-6">
                    <div>
                        <h3 class="text-2xl font-semibold text-slate-900">LMS Fee Invoice</h3>
                        <p class="mt-1 text-sm text-slate-500">Invoice #SF-{{ str_pad($studentFee->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div class="text-right text-sm text-slate-500">
                        <p>Due {{ $studentFee->fee->due_date->format('d M Y') }}</p>
                        <p class="mt-2"><span class="data-pill">{{ $studentFee->status }}</span></p>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div class="module-subcard">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Student</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ $studentFee->student->name }}</p>
                        <p class="text-sm text-slate-500">{{ $studentFee->student->email }}</p>
                    </div>
                    <div class="module-subcard">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Fee</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ $studentFee->fee->title }}</p>
                        <p class="text-sm text-slate-500">{{ $studentFee->fee->classRoom->name }}</p>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <div class="module-subcard">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Amount</p>
                        <p class="mt-2 text-lg font-semibold text-slate-900">{{ number_format($studentFee->total_amount, 2) }}</p>
                    </div>
                    <div class="module-subcard">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Paid Amount</p>
                        <p class="mt-2 text-lg font-semibold text-slate-900">{{ number_format($studentFee->paid_amount, 2) }}</p>
                    </div>
                    <div class="module-subcard">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Pending Amount</p>
                        <p class="mt-2 text-lg font-semibold text-slate-900">{{ number_format($studentFee->pendingAmount(), 2) }}</p>
                    </div>
                </div>

                <div class="mt-8">
                    <h4 class="font-semibold text-slate-900">Payment History</h4>
                    <div class="mt-3 space-y-2">
                        @forelse ($studentFee->payments as $payment)
                            <div class="module-subcard text-sm text-slate-700">
                                {{ number_format($payment->amount, 2) }} via {{ $payment->payment_method }} on {{ $payment->paid_at->format('d M Y') }}
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No payments recorded yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
