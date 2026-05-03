<x-app-layout>
    <x-slot name="header">
        <div class="hero-banner hero-banner-admin">
            <div class="grid gap-6 lg:grid-cols-[1.35fr_0.8fr] lg:items-end">
                <div>
                    <p class="eyebrow">Admin dashboard</p>
                    <p class="sr-only">Admin Dashboard</p>
                    <h2 class="mt-3 text-3xl font-semibold leading-tight sm:text-4xl">
                        Keep the whole campus moving from one command center.
                    </h2>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-white/80">
                        Track curriculum growth, dues, attendance health, and day-to-day operations across every role in the LMS.
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="hero-meta">
                        <p class="text-xs uppercase tracking-[0.2em] text-white/60">People</p>
                        <p class="mt-2 display-font text-3xl font-semibold">{{ $stats['students'] + $stats['teachers'] + $stats['parents'] }}</p>
                    </div>
                    <div class="hero-meta">
                        <p class="text-xs uppercase tracking-[0.2em] text-white/60">Academic units</p>
                        <p class="mt-2 display-font text-3xl font-semibold">{{ $stats['classes'] + $stats['subjects'] }}</p>
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
                        <p class="eyebrow">Campus command center</p>
                        <h3 class="spotlight-title mt-3">One premium view for people, curriculum, and operational health.</h3>
                        <p class="spotlight-copy">Keep an eye on dues, attendance, and staffing while jumping straight into the modules that need attention.</p>

                        <div class="highlight-metrics">
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Pending dues</p>
                                <p class="highlight-metric-value">{{ number_format($stats['total_dues'], 2) }}</p>
                                <p class="highlight-metric-copy">Outstanding fee balance across students.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Attendance average</p>
                                <p class="highlight-metric-value">{{ number_format($stats['attendance_percentage'], 1) }}%</p>
                                <p class="highlight-metric-copy">Current credited attendance trend.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Overdue fees</p>
                                <p class="highlight-metric-value">{{ $stats['overdue_fees'] }}</p>
                                <p class="highlight-metric-copy">Records requiring payment follow-up.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Pending leaves</p>
                                <p class="highlight-metric-value">{{ $stats['pending_leaves'] }}</p>
                                <p class="highlight-metric-copy">Requests still waiting for action.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-rail">
                    <div class="spotlight-panel">
                        <h3 class="section-heading">People Map</h3>
                        <p class="section-copy">A quick read on the current LMS population.</p>

                        <div class="mt-5 space-y-4">
                            <div class="metric-row">
                                <div>
                                    <p class="font-semibold text-slate-900">Teachers</p>
                                    <p class="text-sm text-slate-500">Instruction coverage</p>
                                </div>
                                <span class="data-pill">{{ $stats['teachers'] }}</span>
                            </div>
                            <div class="metric-bar-track">
                                <div class="metric-bar-fill" style="--value: {{ min(100, max(8, ($stats['teachers'] / max(1, $stats['students'] + $stats['teachers'] + $stats['parents'])) * 100)) }}%; --tone: #2563eb;"></div>
                            </div>

                            <div class="metric-row">
                                <div>
                                    <p class="font-semibold text-slate-900">Students</p>
                                    <p class="text-sm text-slate-500">Active learners</p>
                                </div>
                                <span class="data-pill">{{ $stats['students'] }}</span>
                            </div>
                            <div class="metric-bar-track">
                                <div class="metric-bar-fill" style="--value: {{ min(100, max(8, ($stats['students'] / max(1, $stats['students'] + $stats['teachers'] + $stats['parents'])) * 100)) }}%; --tone: #0f766e;"></div>
                            </div>

                            <div class="metric-row">
                                <div>
                                    <p class="font-semibold text-slate-900">Parents</p>
                                    <p class="text-sm text-slate-500">Family accounts</p>
                                </div>
                                <span class="data-pill">{{ $stats['parents'] }}</span>
                            </div>
                            <div class="metric-bar-track">
                                <div class="metric-bar-fill" style="--value: {{ min(100, max(8, ($stats['parents'] / max(1, $stats['students'] + $stats['teachers'] + $stats['parents'])) * 100)) }}%; --tone: #7c3aed;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="spotlight-panel">
                        <h3 class="section-heading">Academic Footprint</h3>
                        <div class="highlight-metrics !mt-5 !grid-cols-2">
                            <div class="highlight-metric-soft">
                                <p class="highlight-metric-label">Classes</p>
                                <p class="highlight-metric-value">{{ $stats['classes'] }}</p>
                                <p class="highlight-metric-copy">Structured learning groups.</p>
                            </div>
                            <div class="highlight-metric-soft">
                                <p class="highlight-metric-label">Subjects</p>
                                <p class="highlight-metric-value">{{ $stats['subjects'] }}</p>
                                <p class="highlight-metric-copy">Taught across the institution.</p>
                            </div>
                            <div class="highlight-metric-soft">
                                <p class="highlight-metric-label">Topics</p>
                                <p class="highlight-metric-value">{{ $stats['topics'] }}</p>
                                <p class="highlight-metric-copy">Current curriculum depth.</p>
                            </div>
                            <div class="highlight-metric-soft">
                                <p class="highlight-metric-label">Units</p>
                                <p class="highlight-metric-value">{{ $stats['classes'] + $stats['subjects'] }}</p>
                                <p class="highlight-metric-copy">Combined academic structure.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-6">
                <a href="{{ route('admin.users') }}" class="action-card">
                    <p class="soft-pill">Users</p>
                    <h3 class="action-title">Create and manage LMS accounts</h3>
                    <p class="action-copy">Create admins, teachers, students, and parents from inside the admin panel.</p>
                </a>

                <a href="{{ route('admin.curriculum') }}" class="action-card">
                    <p class="soft-pill">Curriculum</p>
                    <h3 class="action-title">Manage classes, subjects, and topics</h3>
                    <p class="action-copy">Build the academic structure your teachers and students will use across the LMS.</p>
                </a>

                <a href="{{ route('admin.enrollment') }}" class="action-card">
                    <p class="soft-pill">Enrollment</p>
                    <h3 class="action-title">Enroll students and link parents</h3>
                    <p class="action-copy">Manage student-class enrollment and parent-child relationships from one admin page.</p>
                </a>

                <a href="{{ route('admin.assign-teacher') }}" class="action-card">
                    <p class="soft-pill">Teachers</p>
                    <h3 class="action-title">Assign teachers to classes</h3>
                    <p class="action-copy">Link teachers to the classes they are responsible for so role dashboards can grow from real data.</p>
                </a>

                <a href="{{ route('admin.fees.index') }}" class="action-card">
                    <p class="soft-pill">Fees</p>
                    <h3 class="action-title">Create fees and record payments</h3>
                    <p class="action-copy">Define class fees, assign them to students, and track payment history and pending dues.</p>
                </a>

                <a href="{{ route('admin.attendance.index') }}" class="action-card">
                    <p class="soft-pill">Attendance</p>
                    <h3 class="action-title">Review class attendance reports</h3>
                    <p class="action-copy">Monitor daily attendance summaries and student-level records across all classes.</p>
                </a>
            </div>

            <div class="surface-card">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h3 class="section-heading">Class Overview</h3>
                        <p class="section-copy">Track how curriculum, students, and teachers are distributed across classes.</p>
                    </div>
                </div>

                <div class="mt-5 grid gap-4 lg:grid-cols-2 xl:grid-cols-3">
                    @forelse ($classSummaries as $classRoom)
                        <div class="surface-card-muted">
                            <div class="flex items-center justify-between gap-3">
                                <h4 class="text-lg font-semibold text-slate-900">{{ $classRoom->name }}</h4>
                                <span class="soft-pill !bg-white/90">
                                    {{ $classRoom->type }}
                                </span>
                            </div>

                            <div class="mt-4 grid grid-cols-3 gap-3 text-sm">
                                <div class="rounded-2xl bg-white/90 p-3 text-center ring-1 ring-slate-200/70">
                                    <p class="text-slate-400">Subjects</p>
                                    <p class="mt-1 font-semibold text-slate-900">{{ $classRoom->subjects_count }}</p>
                                </div>
                                <div class="rounded-2xl bg-white/90 p-3 text-center ring-1 ring-slate-200/70">
                                    <p class="text-slate-400">Students</p>
                                    <p class="mt-1 font-semibold text-slate-900">{{ $classRoom->students_count }}</p>
                                </div>
                                <div class="rounded-2xl bg-white/90 p-3 text-center ring-1 ring-slate-200/70">
                                    <p class="text-slate-400">Teachers</p>
                                    <p class="mt-1 font-semibold text-slate-900">{{ $classRoom->teachers_count }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No classes available yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
