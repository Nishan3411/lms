<x-app-layout>
    @php
        $overview = $report['overview'];
        $attendanceSummary = $report['attendance']['summary'];
        $examSummary = $report['exams']['summary'];
        $assignmentSummary = $report['assignments']['summary'];
    @endphp

    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">My Performance Report</h2>
                <p class="module-copy">A polished view of your attendance, scores, and progress in one place.</p>
            </div>

            <a href="{{ route('student.dashboard') }}" class="secondary-action">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell">
            <div class="dashboard-cluster">
                <div class="spotlight-panel-ink">
                    <div class="relative z-10">
                        <p class="eyebrow">Personal report</p>
                        <h3 class="spotlight-title mt-3">Everything important about your progress, without the clutter.</h3>
                        <p class="spotlight-copy">Track how your attendance, exams, and assignments are shaping your overall academic momentum.</p>

                        <div class="highlight-metrics">
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Overall</p>
                                <p class="highlight-metric-value">{{ $overview['overall_percentage'] === null ? 'No data' : number_format($overview['overall_percentage'], 1).'%' }}</p>
                                <p class="highlight-metric-copy">Blended academic performance.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Attendance</p>
                                <p class="highlight-metric-value">{{ number_format($attendanceSummary['percentage'], 1) }}%</p>
                                <p class="highlight-metric-copy">{{ $attendanceSummary['total'] }} attendance records captured.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Exam score</p>
                                <p class="highlight-metric-value">{{ $examSummary['percentage'] === null ? 'No data' : number_format($examSummary['percentage'], 1).'%' }}</p>
                                <p class="highlight-metric-copy">{{ $examSummary['count'] }} exam results published.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Assignments</p>
                                <p class="highlight-metric-value">{{ $assignmentSummary['percentage'] === null ? 'No data' : number_format($assignmentSummary['percentage'], 1).'%' }}</p>
                                <p class="highlight-metric-copy">{{ $assignmentSummary['graded'] }} graded submissions.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-rail">
                    <div class="spotlight-panel">
                        <div class="flex flex-col gap-3">
                            <div>
                                <h3 class="section-heading">Report Scope</h3>
                                <p class="section-copy">A quick sense of the activity and signals included in this report.</p>
                            </div>

                            <div class="mt-2 space-y-4">
                                <div class="metric-row">
                                    <div>
                                        <p class="font-semibold text-slate-900">Student</p>
                                        <p class="text-sm text-slate-500">{{ $student->name }}</p>
                                    </div>
                                    <span class="data-pill">{{ $overview['classes_count'] }} classes</span>
                                </div>
                                <div class="metric-row">
                                    <div>
                                        <p class="font-semibold text-slate-900">Subjects</p>
                                        <p class="text-sm text-slate-500">Included in the performance rollup</p>
                                    </div>
                                    <span class="data-pill">{{ $overview['subjects_count'] }}</span>
                                </div>
                                <div class="metric-row">
                                    <div>
                                        <p class="font-semibold text-slate-900">Teachers</p>
                                        <p class="text-sm text-slate-500">Teaching across your active classes</p>
                                    </div>
                                    <span class="data-pill">{{ $overview['teachers_count'] }}</span>
                                </div>
                            </div>

                            <div class="border-t border-slate-200/70 pt-5">
                                <h4 class="section-heading text-base">Inside This View</h4>
                                <div class="dashboard-list mt-4">
                                    <div class="dashboard-list-item">
                                        <div class="signal-row">
                                            <span class="inline-flex items-center gap-2"><span class="signal-dot bg-emerald-500"></span>Performance charts</span>
                                            <span class="data-pill">Live</span>
                                        </div>
                                    </div>
                                    <div class="dashboard-list-item">
                                        <div class="signal-row">
                                            <span class="inline-flex items-center gap-2"><span class="signal-dot bg-blue-500"></span>Exam summaries</span>
                                            <span class="data-pill">{{ $examSummary['count'] }}</span>
                                        </div>
                                    </div>
                                    <div class="dashboard-list-item">
                                        <div class="signal-row">
                                            <span class="inline-flex items-center gap-2"><span class="signal-dot bg-violet-500"></span>Assignment history</span>
                                            <span class="data-pill">{{ $assignmentSummary['count'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('reports.partials.student-performance', ['student' => $student, 'report' => $report])
        </div>
    </div>
</x-app-layout>
