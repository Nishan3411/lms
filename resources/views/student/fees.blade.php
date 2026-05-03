<x-app-layout>
    @php
        $totalFees = $studentFees->sum('total_amount');
        $totalPaid = $studentFees->sum('paid_amount');
        $totalPending = $studentFees->sum(fn ($studentFee) => $studentFee->pendingAmount());
        $overdueFees = $studentFees->filter(fn ($studentFee) => $studentFee->pendingAmount() > 0 && $studentFee->fee->due_date->isPast())->count();
        $paidFees = $studentFees->where('status', 'paid')->count();
        $partialFees = $studentFees->where('status', 'partial')->count();
        $overallPaidPercentage = $totalFees > 0 ? ($totalPaid / $totalFees) * 100 : 0;
        $compactMoney = function (float|int $amount): string {
            $absolute = abs((float) $amount);

            if ($absolute >= 1000000) {
                return number_format($amount / 1000000, 1).'M';
            }

            if ($absolute >= 1000) {
                return number_format($amount / 1000, 1).'K';
            }

            return number_format($amount, 0);
        };
    @endphp

    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">My Fees</h2>
                <p class="module-copy">Track semester dues, payment history, invoices, and pending balances from one clear fee view.</p>
            </div>

            <a href="{{ route('student.dashboard') }}" class="secondary-action">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell max-w-6xl">
            <div class="space-y-6">
                <div class="spotlight-panel-ink">
                    <div class="relative z-10">
                        <p class="eyebrow">Fee overview</p>
                        <h3 class="spotlight-title mt-3">See how much is settled, what is pending, and what needs attention.</h3>
                        <p class="spotlight-copy">Your fee records, invoices, and payment history stay organized in one premium ledger view.</p>

                        <div class="fee-overview-metrics">
                            <div class="fee-overview-card">
                                <p class="fee-overview-label">Total billed</p>
                                <p class="fee-overview-value">{{ $compactMoney($totalFees) }}</p>
                                <p class="fee-overview-copy">All current fee entries combined.</p>
                                <p class="fee-overview-meta">Exact: {{ number_format($totalFees, 2) }}</p>
                            </div>
                            <div class="fee-overview-card">
                                <p class="fee-overview-label">Paid so far</p>
                                <p class="fee-overview-value">{{ $compactMoney($totalPaid) }}</p>
                                <p class="fee-overview-copy">Recorded successful payments.</p>
                                <p class="fee-overview-meta">Exact: {{ number_format($totalPaid, 2) }}</p>
                            </div>
                            <div class="fee-overview-card">
                                <p class="fee-overview-label">Pending dues</p>
                                <p class="fee-overview-value">{{ $compactMoney($totalPending) }}</p>
                                <p class="fee-overview-copy">Remaining balance across semesters.</p>
                                <p class="fee-overview-meta">Exact: {{ number_format($totalPending, 2) }}</p>
                            </div>
                            <div class="fee-overview-card fee-overview-card-alert">
                                <p class="fee-overview-label">Overdue</p>
                                <p class="fee-overview-value">{{ $overdueFees }}</p>
                                <p class="fee-overview-copy">Fee items past the due date.</p>
                                <p class="fee-overview-meta">Needs follow-up</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="spotlight-panel">
                    <div class="grid gap-6 xl:grid-cols-[0.82fr_1.18fr] xl:items-center">
                        <div>
                            <div class="flex items-end justify-between gap-3">
                                <div>
                                    <p class="text-sm font-medium text-slate-500">Overall paid progress</p>
                                    <p class="mt-1 text-4xl font-semibold text-slate-900">{{ number_format($overallPaidPercentage, 1) }}%</p>
                                </div>
                                <span class="data-pill">{{ $studentFees->count() }} fee items</span>
                            </div>

                            <p class="mt-3 text-sm leading-6 text-slate-500">A quick read on how your fee record is shaping up overall.</p>

                            <div class="fee-progress-track mt-4">
                                <div class="fee-progress-fill" style="--value: {{ min(100, max(0, $overallPaidPercentage)) }}%; --tone: #0f766e;"></div>
                            </div>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-3">
                            <div class="highlight-metric-soft">
                                <p class="highlight-metric-label">Paid</p>
                                <p class="highlight-metric-value">{{ $paidFees }}</p>
                                <p class="highlight-metric-copy">Fully cleared items.</p>
                            </div>
                            <div class="highlight-metric-soft">
                                <p class="highlight-metric-label">Partial</p>
                                <p class="highlight-metric-value">{{ $partialFees }}</p>
                                <p class="highlight-metric-copy">Items with part-payments.</p>
                            </div>
                            <div class="highlight-metric-soft">
                                <p class="highlight-metric-label">Pending</p>
                                <p class="highlight-metric-value">{{ $studentFees->where('status', 'pending')->count() }}</p>
                                <p class="highlight-metric-copy">Items still unpaid.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($studentFees->isEmpty())
                <div class="spotlight-panel text-sm text-slate-500">
                    No fee records are available yet.
                </div>
            @else
                <div class="fee-ledger-stack">
                    @foreach ($studentFees as $studentFee)
                        @php
                            $pendingAmount = $studentFee->pendingAmount();
                            $paidPercentage = $studentFee->total_amount > 0 ? ($studentFee->paid_amount / $studentFee->total_amount) * 100 : 0;
                            $statusClasses = match ($studentFee->status) {
                                'paid' => 'fee-status-chip fee-status-chip-paid',
                                'partial' => 'fee-status-chip fee-status-chip-partial',
                                default => 'fee-status-chip fee-status-chip-pending',
                            };
                        @endphp

                        <div class="fee-ledger-card">
                            <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                                <div class="space-y-4">
                                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                        <div>
                                            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">{{ $studentFee->fee->classRoom->name }}</p>
                                            <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ $studentFee->fee->title }}</h3>
                                            <p class="mt-2 text-sm leading-6 text-slate-500">Due on {{ $studentFee->fee->due_date->format('d M Y') }}</p>
                                        </div>
                                        <span class="{{ $statusClasses }}">{{ ucfirst($studentFee->status) }}</span>
                                    </div>

                                    <div class="fee-breakdown-grid">
                                        <div class="fee-breakdown-card">
                                            <p class="fee-breakdown-label">Total</p>
                                            <p class="fee-breakdown-value">{{ number_format($studentFee->total_amount, 2) }}</p>
                                        </div>
                                        <div class="fee-breakdown-card">
                                            <p class="fee-breakdown-label">Paid</p>
                                            <p class="fee-breakdown-value">{{ number_format($studentFee->paid_amount, 2) }}</p>
                                        </div>
                                        <div class="fee-breakdown-card">
                                            <p class="fee-breakdown-label">Pending</p>
                                            <p class="fee-breakdown-value">{{ number_format($pendingAmount, 2) }}</p>
                                        </div>
                                    </div>

                                    <div class="module-subcard">
                                        <div class="flex items-center justify-between gap-3 text-sm">
                                            <span class="font-semibold text-slate-700">Payment progress</span>
                                            <span class="font-semibold text-slate-900">{{ number_format($paidPercentage, 1) }}%</span>
                                        </div>
                                        <div class="fee-progress-track mt-3">
                                            <div class="fee-progress-fill" style="--value: {{ min(100, max(0, $paidPercentage)) }}%; --tone: {{ $studentFee->status === 'paid' ? '#0f766e' : ($studentFee->status === 'partial' ? '#7c3aed' : '#f59e0b') }};"></div>
                                        </div>
                                    </div>

                                    @if ($pendingAmount > 0 && $studentFee->fee->due_date->isPast())
                                        <div class="notice-error">
                                            Reminder: this fee is overdue and still has an unpaid balance.
                                        </div>
                                    @endif
                                </div>

                                <div class="xl:w-[19rem] xl:min-w-[19rem]">
                                    <div class="module-subcard h-full">
                                        <div class="flex items-center justify-between gap-3">
                                            <div>
                                                <p class="text-sm font-semibold text-slate-900">Documents</p>
                                                <p class="mt-1 text-sm text-slate-500">Access invoice and recorded receipts.</p>
                                            </div>
                                            <span class="data-pill">{{ $studentFee->payments->count() }} payments</span>
                                        </div>

                                        <div class="mt-4 flex flex-wrap gap-3">
                                            <a href="{{ route('student.fees.invoices.show', $studentFee) }}" class="secondary-action">View Invoice</a>
                                        </div>

                                        @if ($pendingAmount > 0)
                                            <div class="mt-5 border-t border-slate-200/70 pt-5">
                                                <p class="text-sm font-semibold text-slate-900">Pay online</p>
                                                <p class="mt-1 text-sm text-slate-500">Choose a full or partial amount and complete the payment securely.</p>

                                                <div class="mt-4">
                                                    @include('fees.partials.razorpay-form', [
                                                        'amount' => $pendingAmount,
                                                        'maxAmount' => $pendingAmount,
                                                        'createUrl' => route('student.fees.razorpay.order', $studentFee),
                                                        'verifyUrlTemplate' => route('student.fees.razorpay.verify', ['paymentAttempt' => '__ATTEMPT__']),
                                                        'buttonLabel' => 'Pay with Razorpay',
                                                        'amountLabel' => 'Amount to pay',
                                                    ])
                                                </div>
                                            </div>
                                        @endif

                                        <div class="mt-5">
                                            <p class="text-sm font-semibold text-slate-900">Payment history</p>
                                            <div class="fee-payment-list mt-3">
                                                @forelse ($studentFee->payments as $payment)
                                                    <div class="fee-payment-item">
                                                        <div class="fee-payment-dot"></div>
                                                        <div class="min-w-0 flex-1">
                                                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                                                <div>
                                                                    <p class="font-semibold text-slate-900">{{ number_format($payment->amount, 2) }}</p>
                                                                    <p class="text-sm text-slate-500">{{ $payment->payment_method }} on {{ $payment->paid_at->format('d M Y') }}</p>
                                                                </div>
                                                                <a href="{{ route('student.fees.payments.receipt', $payment) }}" class="text-sm font-semibold text-slate-700 underline decoration-slate-300 underline-offset-4 hover:text-slate-900">
                                                                    Receipt
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="module-subcard text-sm text-slate-500">
                                                        No payments recorded yet for this fee item.
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
