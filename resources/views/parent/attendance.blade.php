<x-app-layout>
    @php
        $totalAttendanceEntries = $children->sum(fn ($child) => $child->attendance_summary['total']);
        $averageAttendance = $children->count() > 0
            ? $children->avg(fn ($child) => $child->attendance_summary['percentage'])
            : 0;
    @endphp

    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">Children Attendance</h2>
                <p class="module-copy">Track attendance by subject and date for every linked child.</p>
            </div>

            <a href="{{ route('parent.dashboard') }}" class="secondary-action">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell">
            <div class="dashboard-cluster">
                <div class="spotlight-panel-ink">
                    <div class="relative z-10">
                        <p class="eyebrow">Family attendance</p>
                        <h3 class="spotlight-title mt-3">Review each child’s attendance pattern by subject, date, and overall attendance health.</h3>
                        <p class="spotlight-copy">Filter the data first, then move between the matrix view and the detailed record timeline for each child.</p>

                        <div class="highlight-metrics">
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Children</p>
                                <p class="highlight-metric-value">{{ $children->count() }}</p>
                                <p class="highlight-metric-copy">Profiles in the current report.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Average</p>
                                <p class="highlight-metric-value">{{ number_format($averageAttendance, 1) }}%</p>
                                <p class="highlight-metric-copy">Average attendance across linked children.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Entries</p>
                                <p class="highlight-metric-value">{{ $totalAttendanceEntries }}</p>
                                <p class="highlight-metric-copy">Attendance rows inside the current view.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-rail">
                    <div class="spotlight-panel">
                        <h3 class="section-heading">Filter Attendance</h3>
                        <p class="section-copy">Use filters to focus on a class, subject, or date range before reviewing each child.</p>

                        <form method="GET" action="{{ route('parent.attendance.index') }}" class="filter-grid">
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
                        <h3 class="section-heading">Filter Scope</h3>
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
                        </div>
                    </div>
                </div>
            </div>

            @forelse ($children as $child)
                <div class="overview-slab">
                    <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-slate-900">{{ $child->name }}</h3>
                            <p class="mt-1 text-sm text-slate-500">
                                {{ $child->attendance_summary['present'] }} present, {{ $child->attendance_summary['late'] }} late, {{ $child->attendance_summary['absent'] }} absent
                            </p>
                        </div>

                        <div class="module-subcard min-w-[240px]">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-medium text-slate-700">Attendance</span>
                                <span class="font-semibold text-slate-900">{{ number_format($child->attendance_summary['percentage'], 2) }}%</span>
                            </div>
                            <div class="mt-3 h-3 w-full overflow-hidden rounded-full bg-slate-200">
                                <div class="h-full rounded-full bg-emerald-500 transition-all" style="width: {{ min(100, max(0, $child->attendance_summary['percentage'])) }}%;"></div>
                            </div>
                            <p class="mt-2 text-xs text-slate-500">{{ $child->attendance_summary['total'] }} entries recorded.</p>
                            @if ($child->attendance_summary['total'] > 0 && $child->attendance_summary['percentage'] < 75)
                                <p class="notice-warning mt-3 !px-3 !py-2 !text-xs">Low attendance warning: below 75%.</p>
                            @endif
                        </div>
                    </div>

                    <div class="table-shell mt-5">
                        <table class="min-w-full border-collapse text-sm">
                            <thead>
                                <tr class="bg-slate-50/90">
                                    <th class="sticky left-0 bg-slate-50 px-4 py-3 text-left font-semibold text-slate-700">Subject</th>
                                    @foreach ($child->attendance_matrix['dates'] as $date)
                                        <th class="px-3 py-3 text-center font-semibold text-slate-700">{{ \Carbon\Carbon::parse($date)->format('d M') }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($child->attendance_matrix['rows'] as $row)
                                    <tr class="border-t border-slate-200">
                                        <td class="sticky left-0 bg-white px-4 py-3">
                                            <p class="font-medium text-slate-900">{{ $row['subject'] }}</p>
                                            <p class="text-xs text-slate-500">{{ $row['class_room'] }}</p>
                                        </td>
                                        @foreach ($child->attendance_matrix['dates'] as $date)
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
                                        <td colspan="{{ 1 + count($child->attendance_matrix['dates']) }}" class="px-4 py-6 text-center text-slate-500">No attendance matrix available for this child.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5 space-y-3">
                        @forelse ($child->attendanceRecords as $record)
                            <div class="module-subcard flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <h4 class="font-semibold text-slate-900">{{ $record->attendance->subject?->name ?? $record->attendance->classRoom->name }}</h4>
                                    <p class="text-sm text-slate-500">{{ $record->attendance->classRoom->name }} / {{ $record->attendance->date->format('d M Y') }}</p>
                                </div>
                                <span class="state-chip {{ $record->status === 'present' ? 'state-chip-present' : ($record->status === 'late' ? 'state-chip-late' : 'state-chip-absent') }}">
                                    {{ $record->status }}
                                </span>
                            </div>
                        @empty
                            <div class="module-subcard text-sm text-slate-500">
                                No attendance records for this child.
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
</x-app-layout>
