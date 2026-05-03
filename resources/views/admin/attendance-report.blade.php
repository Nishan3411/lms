<x-app-layout>
    @php
        $presentCount = $attendances->sum(fn ($attendance) => $attendance->records->where('status', 'present')->count());
        $lateCount = $attendances->sum(fn ($attendance) => $attendance->records->where('status', 'late')->count());
        $absentCount = $attendances->sum(fn ($attendance) => $attendance->records->where('status', 'absent')->count());
        $recordCount = $presentCount + $lateCount + $absentCount;
    @endphp

    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">Attendance Reports</h2>
                <p class="module-copy">Filter by class, subject, or date and review detailed attendance records.</p>
            </div>

            <a href="{{ route('admin.dashboard') }}" class="secondary-action">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell">
            <div class="dashboard-cluster">
                <div class="spotlight-panel-ink">
                    <div class="relative z-10">
                        <p class="eyebrow">Attendance intelligence</p>
                        <h3 class="spotlight-title mt-3">Watch daily attendance patterns across classes, subjects, and individual student records.</h3>
                        <p class="spotlight-copy">Use filters to narrow the report, then export or inspect the exact sessions that need attention.</p>

                        <div class="highlight-metrics">
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Sessions</p>
                                <p class="highlight-metric-value">{{ $attendances->count() }}</p>
                                <p class="highlight-metric-copy">Attendance sessions in the current report.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Present</p>
                                <p class="highlight-metric-value">{{ $presentCount }}</p>
                                <p class="highlight-metric-copy">Marked present in filtered records.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Late</p>
                                <p class="highlight-metric-value">{{ $lateCount }}</p>
                                <p class="highlight-metric-copy">Marked late in filtered records.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Absent</p>
                                <p class="highlight-metric-value">{{ $absentCount }}</p>
                                <p class="highlight-metric-copy">Marked absent in filtered records.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-rail">
                    <div class="spotlight-panel">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                            <div>
                                <h3 class="section-heading">Filter Records</h3>
                                <p class="section-copy">Narrow the report before exporting or reviewing student-level attendance.</p>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('admin.attendance.export', request()->query()) }}" class="secondary-action">Export CSV</a>
                                <a href="{{ route('admin.attendance.index') }}" class="secondary-action">Clear Filters</a>
                            </div>
                        </div>

                        <form method="GET" action="{{ route('admin.attendance.index') }}" class="filter-grid">
                            <div class="auth-field">
                                <label for="class_room_id" class="auth-label">Class</label>
                                <select id="class_room_id" name="class_room_id" class="auth-input auth-select">
                                    <option value="">All classes</option>
                                    @foreach ($classRooms as $classRoom)
                                        <option value="{{ $classRoom->id }}" @selected((string) ($filters['class_room_id'] ?? '') === (string) $classRoom->id)>{{ $classRoom->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="auth-field">
                                <label for="subject_id" class="auth-label">Subject</label>
                                <select id="subject_id" name="subject_id" class="auth-input auth-select">
                                    <option value="">All subjects</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}" @selected((string) ($filters['subject_id'] ?? '') === (string) $subject->id)>{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="auth-field">
                                <label for="date" class="auth-label">Exact Date</label>
                                <input id="date" type="date" name="date" value="{{ $filters['date'] ?? '' }}" class="auth-input">
                            </div>

                            <div class="auth-field">
                                <label for="month" class="auth-label">Month</label>
                                <input id="month" type="month" name="month" value="{{ $filters['month'] ?? '' }}" class="auth-input">
                            </div>

                            <div class="flex items-end">
                                <button type="submit" class="primary-action w-full">Apply Filters</button>
                            </div>
                        </form>
                    </div>

                    <div class="spotlight-panel">
                        <h3 class="section-heading">Report Scope</h3>
                        <div class="summary-grid !mt-5 !xl:grid-cols-2">
                            <div class="summary-card-soft">
                                <p class="summary-card-soft-label">Classes</p>
                                <p class="summary-card-soft-value">{{ $classRooms->count() }}</p>
                                <p class="summary-card-soft-copy">Available to filter.</p>
                            </div>
                            <div class="summary-card-soft">
                                <p class="summary-card-soft-label">Subjects</p>
                                <p class="summary-card-soft-value">{{ $subjects->count() }}</p>
                                <p class="summary-card-soft-copy">Available to filter.</p>
                            </div>
                            <div class="summary-card-soft">
                                <p class="summary-card-soft-label">Records</p>
                                <p class="summary-card-soft-value">{{ $recordCount }}</p>
                                <p class="summary-card-soft-copy">Attendance entries in the current report.</p>
                            </div>
                            <div class="summary-card-soft">
                                <p class="summary-card-soft-label">Filtered Sessions</p>
                                <p class="summary-card-soft-value">{{ $attendances->count() }}</p>
                                <p class="summary-card-soft-copy">Currently visible attendance groups.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-5">
                @forelse ($attendances as $attendance)
                    <div class="overview-slab">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <h3 class="text-xl font-semibold text-slate-900">{{ $attendance->classRoom->name }}</h3>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $attendance->subject?->name ?? 'General attendance' }} / {{ $attendance->date->format('d M Y') }}
                                </p>
                            </div>

                            <div class="record-badge-row">
                                <span class="state-chip state-chip-present">Present {{ $attendance->records->where('status', 'present')->count() }}</span>
                                <span class="state-chip state-chip-absent">Absent {{ $attendance->records->where('status', 'absent')->count() }}</span>
                                <span class="state-chip state-chip-late">Late {{ $attendance->records->where('status', 'late')->count() }}</span>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                            @foreach ($attendance->records as $record)
                                <div class="module-subcard flex items-center justify-between gap-3">
                                    <span class="text-sm font-medium text-slate-800">{{ $record->student->name }}</span>
                                    <span class="state-chip {{ $record->status === 'present' ? 'state-chip-present' : ($record->status === 'late' ? 'state-chip-late' : 'state-chip-absent') }}">
                                        {{ $record->status }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="spotlight-panel text-sm text-slate-500">
                        No attendance records found for the selected filters.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
