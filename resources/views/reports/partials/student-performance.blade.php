@php
    $overview = $report['overview'];
    $attendanceSummary = $report['attendance']['summary'];
    $examSummary = $report['exams']['summary'];
    $assignmentSummary = $report['assignments']['summary'];
    $recentExamResults = $report['exams']['recent_results']->take(5);
    $recentAssignmentResults = $report['assignments']['recent_submissions']
        ->filter(fn ($submission) => $submission->marks_obtained !== null)
        ->take(5);
    $attendancePresentWidth = $attendanceSummary['total'] > 0 ? ($attendanceSummary['present'] / $attendanceSummary['total']) * 100 : 0;
    $attendanceLateWidth = $attendanceSummary['total'] > 0 ? ($attendanceSummary['late'] / $attendanceSummary['total']) * 100 : 0;
    $attendanceAbsentWidth = $attendanceSummary['total'] > 0 ? ($attendanceSummary['absent'] / $attendanceSummary['total']) * 100 : 0;
@endphp

<div class="space-y-6">
    <div class="grid gap-6 xl:grid-cols-[0.88fr_1.12fr]">
        <div class="module-card">
            <div class="flex flex-col items-center gap-5 text-center">
                <div class="progress-ring progress-ring-xl" style="--value: {{ min(100, max(0, $overview['overall_percentage'] ?? 0)) }}%; --tone: #0f766e;">
                    <div class="progress-ring-inner">
                        <span class="progress-ring-value">{{ $overview['overall_percentage'] === null ? 'N/A' : number_format($overview['overall_percentage'], 1).'%' }}</span>
                        <span class="progress-ring-label">Overall</span>
                    </div>
                </div>

                <div>
                    <h3 class="section-heading">Performance Overview</h3>
                    <p class="section-copy">Attendance, exams, and assignments combined in one quick view.</p>
                </div>

                <div class="grid w-full gap-3">
                    <div class="metric-bar-card">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold text-slate-700">Attendance</span>
                            <span class="font-semibold text-slate-900">{{ number_format($attendanceSummary['percentage'], 1) }}%</span>
                        </div>
                        <div class="metric-bar-track mt-3">
                            <div class="metric-bar-fill" style="--value: {{ min(100, max(0, $attendanceSummary['percentage'])) }}%; --tone: #10b981;"></div>
                        </div>
                    </div>
                    <div class="metric-bar-card">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold text-slate-700">Exams</span>
                            <span class="font-semibold text-slate-900">{{ $examSummary['percentage'] === null ? 'N/A' : number_format($examSummary['percentage'], 1).'%' }}</span>
                        </div>
                        <div class="metric-bar-track mt-3">
                            <div class="metric-bar-fill" style="--value: {{ min(100, max(0, $examSummary['percentage'] ?? 0)) }}%; --tone: #2563eb;"></div>
                        </div>
                    </div>
                    <div class="metric-bar-card">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold text-slate-700">Assignments</span>
                            <span class="font-semibold text-slate-900">{{ $assignmentSummary['percentage'] === null ? 'N/A' : number_format($assignmentSummary['percentage'], 1).'%' }}</span>
                        </div>
                        <div class="metric-bar-track mt-3">
                            <div class="metric-bar-fill" style="--value: {{ min(100, max(0, $assignmentSummary['percentage'] ?? 0)) }}%; --tone: #7c3aed;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="stat-card">
                <p class="text-sm font-medium text-slate-500">Classes</p>
                <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $overview['classes_count'] }}</p>
                <p class="mt-2 text-sm text-slate-500">Active classes in this report.</p>
            </div>
            <div class="stat-card">
                <p class="text-sm font-medium text-slate-500">Subjects</p>
                <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $overview['subjects_count'] }}</p>
                <p class="mt-2 text-sm text-slate-500">Subjects contributing to progress.</p>
            </div>
            <div class="stat-card">
                <p class="text-sm font-medium text-slate-500">Teachers</p>
                <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $overview['teachers_count'] }}</p>
                <p class="mt-2 text-sm text-slate-500">Teachers linked to current classes.</p>
            </div>
            <div class="stat-card">
                <p class="text-sm font-medium text-slate-500">Records</p>
                <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $attendanceSummary['total'] + $examSummary['count'] + $assignmentSummary['graded'] }}</p>
                <p class="mt-2 text-sm text-slate-500">Attendance, exam, and graded assignment entries.</p>
            </div>
        </div>
    </div>

    @if ($overview['warning'])
        <div class="notice-warning">
            {{ $overview['warning'] }}
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="module-card">
            <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div>
                    <h3 class="section-heading">Subject Performance</h3>
                    <p class="section-copy">See how attendance, exams, and assignments are trending by subject.</p>
                </div>
                <div class="flex flex-wrap gap-2 text-xs">
                    <span class="data-pill">{{ $overview['classes_count'] }} classes</span>
                    <span class="data-pill">{{ $overview['subjects_count'] }} subjects</span>
                    <span class="data-pill">{{ $overview['teachers_count'] }} teachers</span>
                </div>
            </div>

            <div class="mt-5 space-y-4">
                @forelse ($report['subject_performance'] as $row)
                    <div class="metric-row">
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $row['label'] }}</p>
                                <p class="text-sm text-slate-500">{{ $row['class_room'] }}</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span class="data-pill">Attendance {{ $row['attendance_percentage'] === null ? 'N/A' : number_format($row['attendance_percentage'], 1).'%' }}</span>
                                <span class="data-pill">Exams {{ $row['exam_percentage'] === null ? 'N/A' : number_format($row['exam_percentage'], 1).'%' }}</span>
                                <span class="data-pill">Assignments {{ $row['assignment_percentage'] === null ? 'N/A' : number_format($row['assignment_percentage'], 1).'%' }}</span>
                            </div>
                        </div>
                        <div class="metric-bar-track mt-4">
                            <div class="metric-bar-fill" style="--value: {{ min(100, max(0, $row['overall_percentage'] ?? 0)) }}%; --tone: #0f766e;"></div>
                        </div>
                    </div>
                @empty
                    <div class="module-subcard text-sm text-slate-500">No performance records available yet.</div>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="module-card">
                <h3 class="section-heading">Academic Snapshot</h3>
                <div class="mt-4 space-y-3">
                    <div class="module-subcard">
                        <p class="text-sm font-semibold text-slate-900">Exam Marks</p>
                        <p class="mt-2 text-sm text-slate-600">
                            {{ $examSummary['count'] > 0 ? number_format($examSummary['obtained'], 2).' / '.number_format($examSummary['max'], 2) : 'No published exam data yet.' }}
                        </p>
                    </div>
                    <div class="module-subcard">
                        <p class="text-sm font-semibold text-slate-900">Assignment Marks</p>
                        <p class="mt-2 text-sm text-slate-600">
                            {{ $assignmentSummary['count'] > 0 ? number_format($assignmentSummary['obtained'], 2).' / '.number_format($assignmentSummary['max'], 2) : 'No graded assignment data yet.' }}
                        </p>
                    </div>
                    <div class="module-subcard">
                        <p class="text-sm font-semibold text-slate-900">Attendance Health</p>
                        <div class="mt-3 h-3 w-full overflow-hidden rounded-full bg-slate-200">
                            <div class="h-full rounded-full bg-emerald-500 transition-all" style="width: {{ min(100, max(0, $attendanceSummary['percentage'])) }}%;"></div>
                        </div>
                        <p class="mt-2 text-sm text-slate-600">{{ $attendanceSummary['total'] }} attendance entries recorded.</p>
                    </div>
                </div>
            </div>

            <div class="module-card">
                <h3 class="section-heading">Attendance Mix</h3>
                <p class="section-copy">A visual split of present, late, and absent records.</p>

                <div class="chart-stack mt-5">
                    <div class="chart-stack-segment chart-stack-green" style="width: {{ $attendancePresentWidth }}%"></div>
                    <div class="chart-stack-segment chart-stack-amber" style="width: {{ $attendanceLateWidth }}%"></div>
                    <div class="chart-stack-segment chart-stack-rose" style="width: {{ $attendanceAbsentWidth }}%"></div>
                </div>

                <div class="mt-4 grid gap-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="inline-flex items-center gap-2 text-slate-600"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>Present</span>
                        <span class="font-semibold text-slate-900">{{ $attendanceSummary['present'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="inline-flex items-center gap-2 text-slate-600"><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>Late</span>
                        <span class="font-semibold text-slate-900">{{ $attendanceSummary['late'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="inline-flex items-center gap-2 text-slate-600"><span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>Absent</span>
                        <span class="font-semibold text-slate-900">{{ $attendanceSummary['absent'] }}</span>
                    </div>
                </div>
            </div>

            <div class="module-card">
                <h3 class="section-heading">Recent Attendance</h3>
                <div class="mt-4 space-y-3">
                    @forelse ($report['attendance']['recent_records'] as $record)
                        <div class="module-subcard flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $record->attendance->subject?->name ?? $record->attendance->classRoom->name }}</p>
                                <p class="text-sm text-slate-500">{{ $record->attendance->classRoom->name }} / {{ $record->attendance->date->format('d M Y') }}</p>
                            </div>
                            <span class="data-pill">{{ ucfirst($record->status) }}</span>
                        </div>
                    @empty
                        <div class="module-subcard text-sm text-slate-500">No attendance entries yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="module-card">
            <h3 class="section-heading">Recent Exam Results</h3>
            <div class="chart-columns mt-5">
                @forelse ($recentExamResults as $result)
                    @php
                        $examPercent = $result->exam->max_marks > 0
                            ? min(100, max(0, ((float) $result->marks_obtained / (float) $result->exam->max_marks) * 100))
                            : 0;
                    @endphp
                    <div class="chart-column">
                        <div class="chart-column-track">
                            <div class="chart-column-bar chart-column-blue" style="height: {{ max(8, $examPercent) }}%"></div>
                        </div>
                        <p class="chart-column-value">{{ number_format($examPercent, 0) }}%</p>
                        <p class="chart-column-label">{{ \Illuminate\Support\Str::limit($result->exam->subject?->name ?? $result->exam->title, 10, '') }}</p>
                    </div>
                @empty
                    <div class="module-subcard w-full text-sm text-slate-500">No recent exam bars yet.</div>
                @endforelse
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($report['exams']['recent_results'] as $result)
                    <div class="module-subcard">
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $result->exam->title }}</p>
                                <p class="text-sm text-slate-500">{{ $result->exam->classRoom->name }} / {{ $result->exam->subject?->name ?? 'General' }} / {{ $result->exam->exam_date->format('d M Y') }}</p>
                                <p class="mt-2 text-sm text-slate-600">{{ $result->remarks ?: 'No remarks.' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-semibold text-slate-900">{{ number_format($result->marks_obtained, 2) }} / {{ $result->exam->max_marks }}</p>
                                <p class="text-sm text-slate-500">{{ $result->grade ?: 'Ungraded' }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="module-subcard text-sm text-slate-500">No published exam results yet.</div>
                @endforelse
            </div>
        </div>

        <div class="module-card">
            <h3 class="section-heading">Recent Assignment Performance</h3>
            <div class="chart-columns mt-5">
                @forelse ($recentAssignmentResults as $submission)
                    @php
                        $assignmentPercent = $submission->assignment->max_marks > 0
                            ? min(100, max(0, ((float) $submission->marks_obtained / (float) $submission->assignment->max_marks) * 100))
                            : 0;
                    @endphp
                    <div class="chart-column">
                        <div class="chart-column-track">
                            <div class="chart-column-bar chart-column-violet" style="height: {{ max(8, $assignmentPercent) }}%"></div>
                        </div>
                        <p class="chart-column-value">{{ number_format($assignmentPercent, 0) }}%</p>
                        <p class="chart-column-label">{{ \Illuminate\Support\Str::limit($submission->assignment->subject?->name ?? $submission->assignment->title, 10, '') }}</p>
                    </div>
                @empty
                    <div class="module-subcard w-full text-sm text-slate-500">No graded assignment bars yet.</div>
                @endforelse
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($report['assignments']['recent_submissions'] as $submission)
                    <div class="module-subcard">
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $submission->assignment->title }}</p>
                                <p class="text-sm text-slate-500">{{ $submission->assignment->classRoom->name }} / {{ $submission->assignment->subject?->name ?? 'General' }}</p>
                                <p class="mt-2 text-sm text-slate-600">{{ $submission->teacher_feedback ?: 'No teacher feedback yet.' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-semibold text-slate-900">
                                    {{ $submission->marks_obtained === null ? 'Pending' : number_format($submission->marks_obtained, 2).' / '.$submission->assignment->max_marks }}
                                </p>
                                <p class="text-sm text-slate-500">{{ ucfirst($submission->status) }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="module-subcard text-sm text-slate-500">No assignment submissions recorded yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
