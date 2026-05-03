<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">My Attendance</h2>
                <p class="module-copy">Review attendance by subject, monitor your percentage, and spot gaps early.</p>
            </div>

            <a href="{{ route('student.dashboard') }}" class="secondary-action">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell">
            <div class="dashboard-cluster">
                <div class="spotlight-panel-ink">
                    <div class="relative z-10">
                        <p class="eyebrow">Attendance overview</p>
                        <h3 class="spotlight-title mt-3">See your attendance percentage, review the subject matrix, and catch low-attendance risks early.</h3>
                        <p class="spotlight-copy">Switch between classes, subjects, and dates to inspect your record without leaving the page.</p>

                        <div class="highlight-metrics">
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Attendance</p>
                                <p class="highlight-metric-value">{{ number_format($summary['percentage'], 1) }}%</p>
                                <p class="highlight-metric-copy">Current credited attendance.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Present</p>
                                <p class="highlight-metric-value">{{ $summary['present'] }}</p>
                                <p class="highlight-metric-copy">Present marks in this report.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Late</p>
                                <p class="highlight-metric-value">{{ $summary['late'] }}</p>
                                <p class="highlight-metric-copy">Late marks in this report.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Absent</p>
                                <p class="highlight-metric-value">{{ $summary['absent'] }}</p>
                                <p class="highlight-metric-copy">Absent marks in this report.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-rail">
                    <div class="spotlight-panel">
                        <h3 class="section-heading">Filter Attendance</h3>
                        <p class="section-copy">Adjust the filters to inspect a class, a subject, or a specific time window.</p>

                        <form method="GET" action="{{ route('student.attendance.index') }}" class="filter-grid">
                            <div class="auth-field">
                                <label class="auth-label" for="class_room_id">Class</label>
                                <select id="class_room_id" name="class_room_id" class="auth-input auth-select">
                                    <option value="">All classes</option>
                                    @foreach ($classRooms as $classRoom)
                                        <option value="{{ $classRoom->id }}" @selected((string) ($filters['class_room_id'] ?? '') === (string) $classRoom->id)>{{ $classRoom->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="auth-field">
                                <label class="auth-label" for="subject_id">Subject</label>
                                <select id="subject_id" name="subject_id" class="auth-input auth-select">
                                    <option value="">All subjects</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}" @selected((string) ($filters['subject_id'] ?? '') === (string) $subject->id)>{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="auth-field">
                                <label class="auth-label" for="date">Exact Date</label>
                                <input id="date" type="date" name="date" value="{{ $filters['date'] ?? '' }}" class="auth-input">
                            </div>
                            <div class="auth-field">
                                <label class="auth-label" for="month">Month</label>
                                <input id="month" type="month" name="month" value="{{ $filters['month'] ?? '' }}" class="auth-input">
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="primary-action w-full">Apply Filters</button>
                            </div>
                        </form>
                    </div>

                    <div class="spotlight-panel">
                        <h3 class="section-heading">Summary</h3>
                        <div class="summary-grid !mt-5 !xl:grid-cols-2">
                            <div class="summary-card-soft">
                                <p class="summary-card-soft-label">Entries</p>
                                <p class="summary-card-soft-value">{{ $summary['total'] }}</p>
                                <p class="summary-card-soft-copy">Attendance entries recorded.</p>
                            </div>
                            <div class="summary-card-soft">
                                <p class="summary-card-soft-label">Attendance</p>
                                <p class="summary-card-soft-value">{{ number_format($summary['percentage'], 1) }}%</p>
                                <p class="summary-card-soft-copy">Current attendance in the filtered view.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overview-slab">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Attendance Summary</h3>
                        <p class="text-sm text-slate-500">Your current percentage across all marked subjects.</p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-semibold text-slate-900">{{ number_format($summary['percentage'], 2) }}%</p>
                        <p class="text-sm text-slate-500">{{ $summary['present'] }} present, {{ $summary['late'] }} late, {{ $summary['absent'] }} absent</p>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="h-3 w-full overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full bg-emerald-500 transition-all" style="width: {{ min(100, max(0, $summary['percentage'])) }}%;"></div>
                    </div>
                    <p class="mt-2 text-sm text-slate-500">{{ $summary['total'] }} attendance entries recorded.</p>
                    @if ($summary['total'] > 0 && $summary['percentage'] < 75)
                        <p class="notice-warning mt-2">Low attendance warning: your attendance is below 75%.</p>
                    @endif
                </div>
            </div>

            <div class="overview-slab">
                <h3 class="text-lg font-semibold text-slate-900">Attendance Matrix</h3>
                <p class="text-sm text-slate-500">Subjects in rows and dates in columns.</p>

                <div class="table-shell mt-5">
                    <table class="min-w-full border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="sticky left-0 bg-slate-50 px-4 py-3 text-left font-semibold text-slate-700">Subject</th>
                                @foreach ($matrix['dates'] as $date)
                                    <th class="px-3 py-3 text-center font-semibold text-slate-700">{{ \Carbon\Carbon::parse($date)->format('d M') }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($matrix['rows'] as $row)
                                <tr class="border-t border-slate-200">
                                    <td class="sticky left-0 bg-white px-4 py-3">
                                        <p class="font-medium text-slate-900">{{ $row['subject'] }}</p>
                                        <p class="text-xs text-slate-500">{{ $row['class_room'] }}</p>
                                    </td>
                                    @foreach ($matrix['dates'] as $date)
                                        @php $status = $row['statuses'][$date] ?? null; @endphp
                                        <td class="px-3 py-3 text-center">
                                            <span class="state-chip {{ $status === 'present' ? 'state-chip-present' : ($status === 'late' ? 'state-chip-late' : ($status === 'absent' ? 'state-chip-absent' : 'state-chip-neutral')) }}">
                                                {{ $status ?? '-' }}
                                            </span>
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 1 + count($matrix['dates']) }}" class="px-4 py-6 text-center text-slate-500">No attendance matrix available yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @forelse ($records as $record)
                <div class="overview-slab">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="font-semibold text-slate-900">{{ $record->attendance->subject?->name ?? $record->attendance->classRoom->name }}</h3>
                            <p class="text-sm text-slate-500">{{ $record->attendance->classRoom->name }} / {{ $record->attendance->date->format('d M Y') }}</p>
                        </div>
                        <span class="state-chip {{ $record->status === 'present' ? 'state-chip-present' : ($record->status === 'late' ? 'state-chip-late' : 'state-chip-absent') }}">
                            {{ $record->status }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="spotlight-panel text-sm text-slate-500">
                    No attendance history is available yet.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
