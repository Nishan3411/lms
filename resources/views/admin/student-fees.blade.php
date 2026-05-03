<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">{{ $student->name }} Fee History</h2>
                <p class="module-copy">Review the full fee timeline, payment entries, and pending balances for this student.</p>
            </div>

            <a href="{{ route('admin.fees.index') }}" class="secondary-action">Back to Fee Management</a>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell max-w-5xl">
            <div class="space-y-5">
                @forelse ($studentFees as $studentFee)
                    <div class="module-card">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">{{ $studentFee->fee->title }}</h3>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $studentFee->fee->classRoom->name }}
                                    -
                                    Due {{ $studentFee->fee->due_date->format('d M Y') }}
                                </p>
                            </div>
                            <span class="data-pill">{{ $studentFee->status }}</span>
                        </div>

                        <div class="mt-5 grid gap-3 md:grid-cols-3">
                            <div class="module-subcard">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Total</p>
                                <p class="mt-2 text-lg font-semibold text-slate-900">{{ number_format($studentFee->total_amount, 2) }}</p>
                            </div>
                            <div class="module-subcard">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Paid</p>
                                <p class="mt-2 text-lg font-semibold text-slate-900">{{ number_format($studentFee->paid_amount, 2) }}</p>
                            </div>
                            <div class="module-subcard">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Pending</p>
                                <p class="mt-2 text-lg font-semibold text-slate-900">{{ number_format($studentFee->pendingAmount(), 2) }}</p>
                            </div>
                        </div>

                        <div class="mt-5">
                            <h4 class="font-semibold text-slate-900">Payments</h4>
                            <div class="mt-3 space-y-2">
                                @forelse ($studentFee->payments as $payment)
                                    <div class="module-subcard flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <span class="text-sm font-medium text-slate-900">{{ number_format($payment->amount, 2) }}</span>
                                        <span class="text-sm text-slate-600">{{ $payment->payment_method }}</span>
                                        <span class="text-sm text-slate-500">{{ $payment->paid_at->format('d M Y') }}</span>
                                    </div>
                                @empty
                                    <div class="module-subcard text-sm text-slate-500">
                                        No payments recorded yet.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="module-card text-sm text-slate-500">
                        This student has no fee records yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
