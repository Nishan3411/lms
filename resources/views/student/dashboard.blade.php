<x-app-layout>
    @php
        $overallPerformance = $report['overview']['overall_percentage'] ?? 0;
        $examPerformance = $report['exams']['summary']['percentage'] ?? 0;
        $assignmentPerformance = $report['assignments']['summary']['percentage'] ?? 0;
        $subjectPulse = $report['subject_performance']->take(4);
        $nextPendingFee = $pendingFees->sortBy(fn ($studentFee) => $studentFee->fee->due_date)->first();
    @endphp

    <x-slot name="header">
        <div class="hero-banner hero-banner-student">
            <div class="grid gap-6 lg:grid-cols-[1.35fr_0.8fr] lg:items-end">
                <div>
                    <p class="eyebrow">Student dashboard</p>
                    <h2 class="mt-3 text-3xl font-semibold leading-tight sm:text-4xl">See what matters today without digging for it.</h2>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-white/80">Your schedule, assignments, attendance, fees, classes, and study material all stay within reach.</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="hero-meta">
                        <p class="text-xs uppercase tracking-[0.2em] text-white/60">Attendance</p>
                        <p class="mt-2 display-font text-3xl font-semibold">{{ number_format($attendancePercentage, 2) }}%</p>
                    </div>
                    <div class="hero-meta">
                        <p class="text-xs uppercase tracking-[0.2em] text-white/60">Due items</p>
                        <p class="mt-2 display-font text-3xl font-semibold">{{ $stats['due_assignments'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <div class="grid gap-4 md:grid-cols-4">
                <div class="action-card">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">My Report</h3>
                            <p class="text-sm text-slate-500">Open one full view for marks, attendance, scores, and overall performance.</p>
                        </div>
                        <a href="{{ route('student.report.show') }}" class="primary-action">
                            View Report
                        </a>
                    </div>
                </div>

                <div class="action-card">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Learning Materials</h3>
                            <p class="text-sm text-slate-500">Download PDF and presentation files uploaded by your teachers.</p>
                        </div>
                        <a href="{{ route('student.materials') }}" class="primary-action">
                            View Materials
                        </a>
                    </div>
                </div>

                <div class="action-card">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Assignments</h3>
                            <p class="text-sm text-slate-500">Submit work and review teacher feedback.</p>
                        </div>
                        <a href="{{ route('student.assignments.index') }}" class="primary-action">
                            View
                        </a>
                    </div>
                </div>

                <div class="action-card">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Fees</h3>
                            <p class="text-sm text-slate-500">Check your fee records, payment history, and pending dues.</p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('student.fees.index') }}" class="secondary-action">
                                View Fees
                            </a>

                            @if ($nextPendingFee)
                                @include('fees.partials.razorpay-form', [
                                    'amount' => $nextPendingFee->pendingAmount(),
                                    'createUrl' => route('student.fees.razorpay.order', $nextPendingFee),
                                    'verifyUrlTemplate' => route('student.fees.razorpay.verify', ['paymentAttempt' => '__ATTEMPT__']),
                                    'buttonLabel' => 'Quick Pay',
                                    'helperText' => 'Pay the next pending fee directly from your dashboard.',
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
                            <p class="text-sm text-slate-500">Review your attendance history across enrolled classes.</p>
                        </div>
                        <a href="{{ route('student.attendance.index') }}" class="primary-action">
                            View Attendance
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-4">
                <div class="stat-card">
                    <p class="text-sm font-medium text-gray-500">Classes</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['classes'] }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-sm font-medium text-gray-500">Subjects</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['subjects'] }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-sm font-medium text-gray-500">Topics</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['topics'] }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-sm font-medium text-gray-500">Linked Parents</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['parents'] }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-sm font-medium text-gray-500">Today's Classes</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['today_classes'] }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-sm font-medium text-gray-500">Due Assignments</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['due_assignments'] }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-sm font-medium text-gray-500">New Materials</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['materials'] }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-sm font-medium text-gray-500">Pending Fees</p>
                    <p class="mt-2 text-3xl font-semibold text-rose-700">{{ number_format($stats['pending_dues'], 2) }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-sm font-medium text-gray-500">Attendance</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ number_format($attendancePercentage, 2) }}%</p>
                </div>
            </div>

            @if ($attendancePercentage > 0 && $attendancePercentage < 75)
                <div class="surface-card !border-amber-200/80 !bg-amber-50/90 p-5 text-sm font-medium text-amber-800">
                    Low attendance warning: your attendance is below 75%.
                </div>
            @endif

            <div class="grid gap-6 xl:grid-cols-[0.92fr_1.08fr]">
                <div class="module-card">
                    <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="section-heading">Performance Snapshot</h3>
                            <p class="section-copy">A quick visual view of your academic health.</p>
                        </div>
                        <div class="progress-ring progress-ring-lg" style="--value: {{ min(100, max(0, $overallPerformance)) }}%; --tone: #0f766e;">
                            <div class="progress-ring-inner">
                                <span class="progress-ring-value">{{ number_format($overallPerformance, 1) }}%</span>
                                <span class="progress-ring-label">Overall</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-3">
                        <div class="metric-bar-card">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-semibold text-slate-700">Attendance</span>
                                <span class="font-semibold text-slate-900">{{ number_format($attendancePercentage, 1) }}%</span>
                            </div>
                            <div class="metric-bar-track mt-3">
                                <div class="metric-bar-fill" style="--value: {{ min(100, max(0, $attendancePercentage)) }}%; --tone: #10b981;"></div>
                            </div>
                        </div>
                        <div class="metric-bar-card">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-semibold text-slate-700">Exams</span>
                                <span class="font-semibold text-slate-900">{{ $report['exams']['summary']['percentage'] === null ? 'N/A' : number_format($examPerformance, 1).'%' }}</span>
                            </div>
                            <div class="metric-bar-track mt-3">
                                <div class="metric-bar-fill" style="--value: {{ min(100, max(0, $examPerformance)) }}%; --tone: #2563eb;"></div>
                            </div>
                        </div>
                        <div class="metric-bar-card">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-semibold text-slate-700">Assignments</span>
                                <span class="font-semibold text-slate-900">{{ $report['assignments']['summary']['percentage'] === null ? 'N/A' : number_format($assignmentPerformance, 1).'%' }}</span>
                            </div>
                            <div class="metric-bar-track mt-3">
                                <div class="metric-bar-fill" style="--value: {{ min(100, max(0, $assignmentPerformance)) }}%; --tone: #7c3aed;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="module-card">
                    <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                        <div>
                            <h3 class="section-heading">Subject Progress</h3>
                            <p class="section-copy">Your top subjects based on recent performance.</p>
                        </div>
                        <a href="{{ route('student.report.show') }}" class="secondary-action">Open Full Report</a>
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse ($subjectPulse as $row)
                            <div class="metric-row">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $row['label'] }}</p>
                                        <p class="text-sm text-slate-500">{{ $row['class_room'] }}</p>
                                    </div>
                                    <span class="data-pill">{{ $row['overall_percentage'] === null ? 'N/A' : number_format($row['overall_percentage'], 1).'%' }}</span>
                                </div>
                                <div class="metric-bar-track mt-3">
                                    <div class="metric-bar-fill" style="--value: {{ min(100, max(0, $row['overall_percentage'] ?? 0)) }}%; --tone: #0f766e;"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Performance bars will appear here as your work gets graded.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="surface-card">
                    <h3 class="section-heading">Today's Schedule</h3>
                    <div class="mt-4 space-y-3">
                        @forelse ($todaySchedule as $entry)
                            <div class="surface-card-muted !rounded-2xl !p-4 text-sm text-slate-700">
                                <span class="font-semibold text-slate-900">{{ substr($entry->starts_at, 0, 5) }}-{{ substr($entry->ends_at, 0, 5) }}</span>
                                - {{ $entry->classRoom->name }} / {{ $entry->subject->name }} with {{ $entry->teacher->name }}
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No classes scheduled today.</p>
                        @endforelse
                    </div>
                </div>

                <div class="surface-card">
                    <h3 class="section-heading">Assignments Due</h3>
                    <div class="mt-4 space-y-3">
                        @forelse ($dueAssignments as $assignment)
                            <div class="surface-card-muted !rounded-2xl !p-4 text-sm text-slate-700">
                                <span class="font-semibold text-slate-900">{{ $assignment->title }}</span>
                                - {{ $assignment->classRoom->name }} / {{ $assignment->subject?->name ?? 'General' }} due {{ $assignment->due_at->format('d M Y') }}
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No pending assignments.</p>
                        @endforelse
                    </div>
                </div>

                <div class="surface-card">
                    <h3 class="section-heading">Latest Materials</h3>
                    <div class="mt-4 space-y-3">
                        @forelse ($latestMaterials as $material)
                            <div class="surface-card-muted !rounded-2xl !p-4 text-sm text-slate-700">
                                <span class="font-semibold text-slate-900">{{ $material->title }}</span>
                                - {{ $material->classRoom->name }} / {{ $material->subject?->name ?? 'General' }}
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No learning materials available yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[2fr_1fr]">
                <div class="surface-card">
                    <h3 class="section-heading">My Classes</h3>
                    <p class="section-copy">Your enrolled classes, teachers, and available learning topics.</p>

                    <div class="mt-5 space-y-4">
                        @forelse ($enrolledClasses as $classRoom)
                            <div class="surface-card-muted">
                                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                                    <div>
                                        <h4 class="text-lg font-semibold text-slate-900">{{ $classRoom->name }}</h4>
                                        <p class="text-sm text-slate-500">{{ ucfirst($classRoom->type) }} class</p>
                                    </div>
                                    <div class="text-sm text-slate-600">
                                        Teachers:
                                        {{ $classRoom->teachers->pluck('name')->join(', ') ?: 'Not assigned yet' }}
                                    </div>
                                </div>

                                <div class="mt-4 grid gap-3 md:grid-cols-2">
                                    @forelse ($classRoom->subjects as $subject)
                                        <div class="rounded-2xl bg-white/90 p-4 ring-1 ring-slate-200/70">
                                            <h5 class="font-semibold text-slate-900">{{ $subject->name }}</h5>
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                @forelse ($subject->topics as $topic)
                                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                                                        {{ $topic->title }}
                                                    </span>
                                                @empty
                                                    <span class="text-sm text-slate-500">No topics yet.</span>
                                                @endforelse
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-sm text-slate-500">No subjects available yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">You are not enrolled in any classes yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="surface-card">
                    <h3 class="section-heading">Parents</h3>
                    <p class="section-copy">Parents linked to your profile.</p>

                    <div class="mt-5 space-y-3">
                        @forelse ($student->parents as $parent)
                            <div class="surface-card-muted !rounded-2xl !px-4 !py-3 text-sm font-medium text-slate-700">
                                {{ $parent->name }}
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No parents linked yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
