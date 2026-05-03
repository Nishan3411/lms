<x-app-layout>
    @php
        $allFees = $children->flatMap->fees;
        $totalFees = $allFees->sum('total_amount');
        $totalPaid = $allFees->sum('paid_amount');
        $totalPending = $allFees->sum(fn ($studentFee) => $studentFee->pendingAmount());
        $overdueFees = $allFees->filter(fn ($studentFee) => $studentFee->pendingAmount() > 0 && $studentFee->fee->due_date->isPast())->count();
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
                <h2 class="module-title">Children Fees</h2>
                <p class="module-copy">Review invoices, receipts, pending dues, and payment history for linked children.</p>
            </div>

            <a href="{{ route('parent.dashboard') }}" class="secondary-action">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell max-w-6xl">
            <div class="space-y-6">
                <div class="spotlight-panel-ink">
                    <div class="relative z-10">
                        <p class="eyebrow">Family fee overview</p>
                        <h3 class="spotlight-title mt-3">Keep every child’s dues, receipts, and online payments in one clear finance view.</h3>
                        <p class="spotlight-copy">Track what is cleared, what is overdue, and what still needs action across all linked students.</p>

                        <div class="fee-overview-metrics">
                            <div class="fee-overview-card">
                                <p class="fee-overview-label">Total billed</p>
                                <p class="fee-overview-value">{{ $compactMoney($totalFees) }}</p>
                                <p class="fee-overview-copy">All child fee entries combined.</p>
                                <p class="fee-overview-meta">Exact: {{ number_format($totalFees, 2) }}</p>
                            </div>
                            <div class="fee-overview-card">
                                <p class="fee-overview-label">Paid so far</p>
                                <p class="fee-overview-value">{{ $compactMoney($totalPaid) }}</p>
                                <p class="fee-overview-copy">Recorded payments across linked children.</p>
                                <p class="fee-overview-meta">Exact: {{ number_format($totalPaid, 2) }}</p>
                            </div>
                            <div class="fee-overview-card">
                                <p class="fee-overview-label">Pending dues</p>
                                <p class="fee-overview-value">{{ $compactMoney($totalPending) }}</p>
                                <p class="fee-overview-copy">Remaining balances still open.</p>
                                <p class="fee-overview-meta">Exact: {{ number_format($totalPending, 2) }}</p>
                            </div>
                            <div class="fee-overview-card fee-overview-card-alert">
                                <p class="fee-overview-label">Overdue</p>
                                <p class="fee-overview-value">{{ $overdueFees }}</p>
                                <p class="fee-overview-copy">Fee items past their due date.</p>
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
                                <span class="data-pill">{{ $allFees->count() }} fee items</span>
                            </div>

                            <p class="mt-3 text-sm leading-6 text-slate-500">A combined view of how family fee obligations are progressing.</p>

                            <div class="fee-progress-track mt-4">
                                <div class="fee-progress-fill" style="--value: {{ min(100, max(0, $overallPaidPercentage)) }}%; --tone: #0f766e;"></div>
                            </div>
                        </div>

                        <div class="summary-grid !xl:grid-cols-3">
                            <div class="summary-card-soft">
                                <p class="summary-card-soft-label">Children</p>
                                <p class="summary-card-soft-value">{{ $children->count() }}</p>
                                <p class="summary-card-soft-copy">Linked student profiles.</p>
                            </div>
                            <div class="summary-card-soft">
                                <p class="summary-card-soft-label">Pending</p>
                                <p class="summary-card-soft-value">{{ $allFees->where('status', 'pending')->count() }}</p>
                                <p class="summary-card-soft-copy">Unpaid fee items.</p>
                            </div>
                            <div class="summary-card-soft">
                                <p class="summary-card-soft-label">Partial</p>
                                <p class="summary-card-soft-value">{{ $allFees->where('status', 'partial')->count() }}</p>
                                <p class="summary-card-soft-copy">Items with part-payments.</p>
                            </div>
                        </div>
                    </div>
                </div>

                @forelse ($children as $child)
                    <div class="overview-slab">
                        <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                            <div>
                                <p class="eyebrow !text-slate-500">Child ledger</p>
                                <h3 class="mt-3 text-3xl font-semibold text-slate-900">{{ $child->name }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-500">Fee status across assigned semesters, dues, and recorded receipts.</p>
                            </div>

                            <div class="record-badge-row">
                                <span class="data-pill">{{ $child->fees->count() }} fee items</span>
                                <span class="data-pill">Pending {{ number_format($child->fees->sum(fn ($studentFee) => $studentFee->pendingAmount()), 2) }}</span>
                            </div>
                        </div>

                        <div class="fee-ledger-stack mt-6">
                            @forelse ($child->fees as $studentFee)
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
                                                    <h4 class="mt-2 text-2xl font-semibold text-slate-900">{{ $studentFee->fee->title }}</h4>
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
                                                    <a href="{{ route('parent.fees.invoices.show', $studentFee) }}" class="secondary-action">View Invoice</a>
                                                </div>

                                                @if ($pendingAmount > 0)
                                                    <div class="mt-5 border-t border-slate-200/70 pt-5">
                                                        <p class="text-sm font-semibold text-slate-900">Pay online for {{ $child->name }}</p>
                                                        <p class="mt-1 text-sm text-slate-500">You can pay the full due or enter a partial amount through Razorpay.</p>

                                                        <div class="mt-4">
                                                            @include('fees.partials.razorpay-form', [
                                                                'amount' => $pendingAmount,
                                                                'maxAmount' => $pendingAmount,
                                                                'createUrl' => route('parent.fees.razorpay.order', $studentFee),
                                                                'verifyUrlTemplate' => route('parent.fees.razorpay.verify', ['paymentAttempt' => '__ATTEMPT__']),
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
                                                                        <a href="{{ route('parent.fees.payments.receipt', $payment) }}" class="text-sm font-semibold text-slate-700 underline decoration-slate-300 underline-offset-4 hover:text-slate-900">
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
                            @empty
                                <div class="module-subcard text-sm text-slate-500">
                                    No fee records for this child.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="spotlight-panel text-sm text-slate-500">
                        No children are linked to this account yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
