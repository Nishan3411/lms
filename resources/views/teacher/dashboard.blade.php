
<x-app-layout>
    <x-slot name="header">
        <div class="hero-banner hero-banner-teacher">
            <div class="grid gap-6 lg:grid-cols-[1.35fr_0.8fr] lg:items-end">
                <div>
                    <p class="eyebrow">Teacher dashboard</p>
                    <h2 class="mt-3 text-3xl font-semibold leading-tight sm:text-4xl">Teach with less clutter and clearer priorities.</h2>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-white/80">See your classes, pending work, materials, and today's schedule in one focused workspace.</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="hero-meta">
                        <p class="text-xs uppercase tracking-[0.2em] text-white/60">Classes</p>
                        <p class="mt-2 display-font text-3xl font-semibold">{{ $stats['classes'] }}</p>
                    </div>
                    <div class="hero-meta">
                        <p class="text-xs uppercase tracking-[0.2em] text-white/60">To grade</p>
                        <p class="mt-2 display-font text-3xl font-semibold">{{ $stats['to_grade'] }}</p>
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
                        <p class="eyebrow">Teaching pulse</p>
                        <h3 class="spotlight-title mt-3">A calmer command center for teaching, grading, and daily class rhythm.</h3>
                        <p class="spotlight-copy">Keep the repeated work close at hand while your class load, schedule, and pending items stay visible in one place.</p>

                        <div class="highlight-metrics">
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Assigned classes</p>
                                <p class="highlight-metric-value">{{ $stats['classes'] }}</p>
                                <p class="highlight-metric-copy">Active classes under your care.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Students</p>
                                <p class="highlight-metric-value">{{ $stats['students'] }}</p>
                                <p class="highlight-metric-copy">Learners across your timetable.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">To grade</p>
                                <p class="highlight-metric-value">{{ $stats['to_grade'] }}</p>
                                <p class="highlight-metric-copy">Submissions still awaiting review.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Pending attendance</p>
                                <p class="highlight-metric-value">{{ $stats['pending_attendance'] }}</p>
                                <p class="highlight-metric-copy">Attendance entries still to mark.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-rail">
                    <div class="spotlight-panel">
                        <h3 class="section-heading">Workload Balance</h3>
                        <p class="section-copy">See where today's effort is likely to go.</p>

                        <div class="mt-5 space-y-4">
                            <div class="metric-row">
                                <span class="font-semibold text-slate-900">Classes today</span>
                                <span class="data-pill">{{ $stats['today_classes'] }}</span>
                            </div>
                            <div class="metric-bar-track">
                                <div class="metric-bar-fill" style="--value: {{ min(100, max(8, $stats['today_classes'] * 20)) }}%; --tone: #0f766e;"></div>
                            </div>

                            <div class="metric-row">
                                <span class="font-semibold text-slate-900">Submissions to grade</span>
                                <span class="data-pill">{{ $stats['to_grade'] }}</span>
                            </div>
                            <div class="metric-bar-track">
                                <div class="metric-bar-fill" style="--value: {{ min(100, max(8, $stats['to_grade'] * 10)) }}%; --tone: #7c3aed;"></div>
                            </div>

                            <div class="metric-row">
                                <span class="font-semibold text-slate-900">Attendance still pending</span>
                                <span class="data-pill">{{ $stats['pending_attendance'] }}</span>
                            </div>
                            <div class="metric-bar-track">
                                <div class="metric-bar-fill" style="--value: {{ min(100, max(8, $stats['pending_attendance'] * 10)) }}%; --tone: #f59e0b;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="spotlight-panel">
                        <h3 class="section-heading">Resource Output</h3>
                        <div class="highlight-metrics !mt-5 !grid-cols-2">
                            <div class="highlight-metric-soft">
                                <p class="highlight-metric-label">Materials</p>
                                <p class="highlight-metric-value">{{ $stats['materials'] }}</p>
                                <p class="highlight-metric-copy">Published learning assets.</p>
                            </div>
                            <div class="highlight-metric-soft">
                                <p class="highlight-metric-label">Subjects</p>
                                <p class="highlight-metric-value">{{ $stats['subjects'] }}</p>
                                <p class="highlight-metric-copy">Across assigned classes.</p>
                            </div>
                            <div class="highlight-metric-soft">
                                <p class="highlight-metric-label">Topics</p>
                                <p class="highlight-metric-value">{{ $stats['topics'] }}</p>
                                <p class="highlight-metric-copy">Current curriculum coverage.</p>
                            </div>
                            <div class="highlight-metric-soft">
                                <p class="highlight-metric-label">Students</p>
                                <p class="highlight-metric-value">{{ $stats['students'] }}</p>
                                <p class="highlight-metric-copy">Across your class network.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-4">
                <div class="action-card">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Student Reports</h3>
                            <p class="text-sm text-slate-500">Review a student's attendance, scores, exam marks, and performance from one page.</p>
                        </div>
                        <a href="{{ route('teacher.student-reports.index') }}" class="primary-action">
                            Open
                        </a>
                    </div>
                </div>

                <div class="action-card">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Learning Materials</h3>
                            <p class="text-sm text-slate-500">Upload PDF, PPT, and PPTX files for your assigned classes.</p>
                        </div>
                        <a href="{{ route('teacher.materials') }}" class="primary-action">
                            Manage Materials
                        </a>
                    </div>
                </div>

                <div class="action-card">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Assignments</h3>
                            <p class="text-sm text-slate-500">Create assignments and review student submissions.</p>
                        </div>
                        <a href="{{ route('teacher.assignments.index') }}" class="primary-action">
                            Manage
                        </a>
                    </div>
                </div>

                <div class="action-card">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Attendance</h3>
                            <p class="text-sm text-slate-500">Mark daily attendance for students in your assigned classes.</p>
                        </div>
                        <a href="{{ route('teacher.attendance.index') }}" class="primary-action">
                            Mark Attendance
                        </a>
                    </div>
                </div>
            </div>

            <div class="dashboard-bento">
                <div class="stat-card">
                    <p class="text-sm font-medium text-gray-500">Assigned Classes</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['classes'] }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-sm font-medium text-gray-500">Students</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['students'] }}</p>
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
                    <p class="text-sm font-medium text-gray-500">Today Classes</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['today_classes'] }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-sm font-medium text-gray-500">Pending Attendance</p>
                    <p class="mt-2 text-3xl font-semibold text-amber-700">{{ $stats['pending_attendance'] }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-sm font-medium text-gray-500">Materials Uploaded</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['materials'] }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-sm font-medium text-gray-500">Assignments To Grade</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['to_grade'] }}</p>
                </div>
            </div>

            <div class="surface-card">
                <h3 class="section-heading">Today's Schedule</h3>
                <div class="mt-4 space-y-3">
                    @forelse ($upcomingClasses as $entry)
                        <div class="surface-card-muted !rounded-2xl !p-4 text-sm text-slate-700">
                            <span class="font-semibold text-slate-900">{{ substr($entry->starts_at, 0, 5) }}-{{ substr($entry->ends_at, 0, 5) }}</span>
                            - {{ $entry->classRoom->name }} / {{ $entry->subject->name }}
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No classes scheduled today.</p>
                    @endforelse
                </div>
            </div>

            <div class="surface-card">
                <h3 class="section-heading">Assigned Classes</h3>
                <p class="section-copy">Each class includes its subject structure and topic breakdown.</p>

                <div class="mt-5 space-y-4">
                    @forelse ($teachingClasses as $classRoom)
                        <div class="surface-card-muted">
                            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <h4 class="text-lg font-semibold text-slate-900">{{ $classRoom->name }}</h4>
                                    <p class="text-sm text-slate-500">{{ ucfirst($classRoom->type) }} class</p>
                                </div>
                                <div class="flex gap-3 text-sm text-slate-600">
                                    <span>{{ $classRoom->students_count }} students</span>
                                    <span>{{ $classRoom->subjects_count }} subjects</span>
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
                                    <p class="text-sm text-slate-500">No subjects assigned to this class yet.</p>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">You do not have any assigned classes yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
