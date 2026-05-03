<x-app-layout>
    @php
        $nextPendingFee = $children
            ->flatMap(fn ($child) => $child->fees->whereIn('status', ['pending', 'partial']))
            ->sortBy(fn ($studentFee) => $studentFee->fee->due_date)
            ->first();
    @endphp

    <x-slot name="header">
        <div class="hero-banner hero-banner-parent">
            <div class="grid gap-6 lg:grid-cols-[1.35fr_0.8fr] lg:items-end">
                <div>
                    <p class="eyebrow">Parent dashboard</p>
                    <h2 class="mt-3 text-3xl font-semibold leading-tight sm:text-4xl">Stay close to your child's school life without the noise.</h2>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-white/80">Track attendance, dues, results, teachers, class progress, and study materials from one reassuring view.</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="hero-meta">
                        <p class="text-xs uppercase tracking-[0.2em] text-white/60">Children</p>
                        <p class="mt-2 display-font text-3xl font-semibold">{{ $stats['children'] }}</p>
                    </div>
                    <div class="hero-meta">
                        <p class="text-xs uppercase tracking-[0.2em] text-white/60">Subjects tracked</p>
                        <p class="mt-2 display-font text-3xl font-semibold">{{ $stats['subjects'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <div class="dashboard-cluster">
                <div class="spotlight-panel-ink">
                    <div class="relative z-10">
                        <p class="eyebrow">Family overview</p>
                        <h3 class="spotlight-title mt-3">A premium parent view for progress, alerts, and daily reassurance.</h3>
                        <p class="spotlight-copy">See attendance, dues, results, and classroom updates without hunting across modules.</p>

                        <div class="highlight-metrics">
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Children</p>
                                <p class="highlight-metric-value">{{ $stats['children'] }}</p>
                                <p class="highlight-metric-copy">Linked student profiles.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Classes</p>
                                <p class="highlight-metric-value">{{ $stats['classes'] }}</p>
                                <p class="highlight-metric-copy">Classrooms being tracked.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Subjects</p>
                                <p class="highlight-metric-value">{{ $stats['subjects'] }}</p>
                                <p class="highlight-metric-copy">Subjects across linked children.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Topics</p>
                                <p class="highlight-metric-value">{{ $stats['topics'] }}</p>
                                <p class="highlight-metric-copy">Visible curriculum depth.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-rail">
                    <div class="spotlight-panel">
                        <h3 class="section-heading">Family Pulse</h3>
                        <p class="section-copy">A quick view of each child's current academic state.</p>

                        <div class="dashboard-list mt-5">
                            @forelse ($children as $child)
                                <div class="dashboard-list-item">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ $child->name }}</p>
                                            <p class="text-sm text-slate-500">{{ $child->enrolledClasses->count() }} classes</p>
                                        </div>
                                        <span class="data-pill">{{ number_format($child->attendance_percentage, 1) }}%</span>
                                    </div>
                                    <div class="metric-bar-track mt-3">
                                        <div class="metric-bar-fill" style="--value: {{ min(100, max(0, $child->attendance_percentage)) }}%; --tone: #0f766e;"></div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">No linked children yet.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="spotlight-panel">
                        <h3 class="section-heading">Quick Access</h3>
                        <div class="highlight-metrics !mt-5 !grid-cols-2">
                            <div class="highlight-metric-soft">
                                <p class="highlight-metric-label">Reports</p>
                                <p class="highlight-metric-value">{{ $stats['children'] }}</p>
                                <p class="highlight-metric-copy">Children with full report views.</p>
                            </div>
                            <div class="highlight-metric-soft">
                                <p class="highlight-metric-label">Attendance</p>
                                <p class="highlight-metric-value">{{ $stats['classes'] }}</p>
                                <p class="highlight-metric-copy">Tracked class connections.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="action-card">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Fees</h3>
                            <p class="text-sm text-slate-500">Review payment history and pending fee dues for your linked children.</p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('parent.fees.index') }}" class="secondary-action">
                                View Fees
                            </a>

                            @if ($nextPendingFee)
                                @include('fees.partials.razorpay-form', [
                                    'amount' => $nextPendingFee->pendingAmount(),
                                    'createUrl' => route('parent.fees.razorpay.order', $nextPendingFee),
                                    'verifyUrlTemplate' => route('parent.fees.razorpay.verify', ['paymentAttempt' => '__ATTEMPT__']),
                                    'buttonLabel' => 'Quick Pay',
                                    'helperText' => 'Pay the nearest pending child fee directly from your dashboard.',
                                    'showAmountInput' => false,
                                ])
                            @endif
                        </div>
                    </div>
                </div>

                <div class="action-card">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Attendance</h3>
                            <p class="text-sm text-slate-500">Track attendance history for every child linked to this parent account.</p>
                        </div>
                        <a href="{{ route('parent.attendance.index') }}" class="primary-action">
                            View Attendance
                        </a>
                    </div>
                </div>
            </div>

            <div class="surface-card">
                <h3 class="section-heading">Children Overview</h3>
                <p class="section-copy">Review each child's enrolled classes and assigned teachers.</p>

                <div class="mt-5 space-y-4">
                    @forelse ($children as $child)
                        <div class="surface-card-muted">
                            <h4 class="text-lg font-semibold text-slate-900">{{ $child->name }}</h4>
                            <div class="mt-3 grid gap-3 md:grid-cols-3">
                                <div class="rounded-2xl bg-white/90 p-4 ring-1 ring-slate-200/70">
                                    <p class="text-sm text-slate-500">Attendance</p>
                                    <p class="mt-1 text-xl font-semibold text-slate-900">{{ number_format($child->attendance_percentage, 2) }}%</p>
                                    @if ($child->attendance_percentage > 0 && $child->attendance_percentage < 75)
                                        <p class="mt-2 text-xs font-medium text-amber-700">Low attendance warning.</p>
                                    @endif
                                </div>
                                <div class="rounded-2xl bg-white/90 p-4 ring-1 ring-slate-200/70">
                                    <p class="text-sm text-slate-500">Pending Dues</p>
                                    <p class="mt-1 text-xl font-semibold text-rose-700">{{ number_format($child->pending_dues, 2) }}</p>
                                </div>
                                <div class="rounded-2xl bg-white/90 p-4 ring-1 ring-slate-200/70">
                                    <p class="text-sm text-slate-500">Result Average</p>
                                    <p class="mt-1 text-xl font-semibold text-slate-900">{{ $child->result_average === null ? 'N/A' : number_format($child->result_average, 2).'%' }}</p>
                                </div>
                            </div>

                            <div class="mt-4 rounded-2xl bg-white/90 p-4 ring-1 ring-slate-200/70">
                                <p class="font-semibold text-slate-900">Recent Materials</p>
                                <div class="mt-3 space-y-2">
                                    @forelse ($child->latest_materials as $material)
                                        <p class="text-sm text-slate-600">{{ $material->title }} - {{ $material->classRoom->name }} / {{ $material->subject?->name ?? 'General' }}</p>
                                    @empty
                                        <p class="text-sm text-slate-500">No materials available yet.</p>
                                    @endforelse
                                </div>
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('parent.student-reports.index') }}#student-report-{{ $child->id }}" class="primary-action">
                                    View Full Report
                                </a>
                            </div>

                            <div class="mt-4 space-y-3">
                                @forelse ($child->enrolledClasses as $classRoom)
                                    <div class="rounded-2xl bg-white/90 p-4 ring-1 ring-slate-200/70">
                                        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                                            <div>
                                                <h5 class="font-semibold text-slate-900">{{ $classRoom->name }}</h5>
                                                <p class="text-sm text-slate-500">{{ ucfirst($classRoom->type) }} class</p>
                                            </div>
                                            <div class="text-sm text-slate-600">
                                                Teachers:
                                                {{ $classRoom->teachers->pluck('name')->join(', ') ?: 'Not assigned yet' }}
                                            </div>
                                        </div>

                                        <p class="mt-3 text-sm text-slate-500">
                                            {{ $classRoom->subjects->count() }} subjects and
                                            {{ $classRoom->subjects->sum(fn ($subject) => $subject->topics->count()) }} topics available.
                                        </p>
                                    </div>
                                @empty
                                    <p class="text-sm text-slate-500">No classes linked for this child yet.</p>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No children linked to this account yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
