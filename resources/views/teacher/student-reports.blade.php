<x-app-layout>
    @php
        $selectedOverview = $report['overview'] ?? null;
        $selectedAttendance = $report['attendance']['summary'] ?? null;
        $selectedExams = $report['exams']['summary'] ?? null;
        $selectedAssignments = $report['assignments']['summary'] ?? null;
    @endphp

    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">Student Reports</h2>
                <p class="module-copy">Open a premium student progress view from your assigned classes.</p>
            </div>

            <a href="{{ route('teacher.dashboard') }}" class="secondary-action">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell">
            <div class="dashboard-cluster">
                <div class="spotlight-panel-ink">
                    <div class="relative z-10">
                        <p class="eyebrow">Teacher report center</p>
                        <h3 class="spotlight-title mt-3">Move from class filters to individual student insight in one clean flow.</h3>
                        <p class="spotlight-copy">Choose a class, open a learner report, and review attendance, exam performance, and graded work without switching pages.</p>

                        <div class="highlight-metrics">
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Assigned classes</p>
                                <p class="highlight-metric-value">{{ $classRooms->count() }}</p>
                                <p class="highlight-metric-copy">Classes available for report review.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Students in view</p>
                                <p class="highlight-metric-value">{{ $students->count() }}</p>
                                <p class="highlight-metric-copy">Students matching the current filter.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Selected overall</p>
                                <p class="highlight-metric-value">{{ $selectedOverview ? ($selectedOverview['overall_percentage'] === null ? 'No data' : number_format($selectedOverview['overall_percentage'], 1).'%' ) : 'Select' }}</p>
                                <p class="highlight-metric-copy">Current student performance pulse.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Attendance</p>
                                <p class="highlight-metric-value">{{ $selectedAttendance ? number_format($selectedAttendance['percentage'], 1).'%' : 'Select' }}</p>
                                <p class="highlight-metric-copy">Attendance health for the selected learner.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-rail">
                    <div class="spotlight-panel">
                        <h3 class="section-heading">Choose Student</h3>
                        <p class="section-copy">Narrow by class first if you want a smaller list, then open one report at a time.</p>

                        <form method="GET" action="{{ route('teacher.student-reports.index') }}" class="mt-5 grid gap-4">
                            <select name="class_room_id" class="auth-input auth-select">
                                <option value="">All assigned classes</option>
                                @foreach ($classRooms as $classRoom)
                                    <option value="{{ $classRoom->id }}" @selected((string) $selectedClassRoomId === (string) $classRoom->id)>{{ $classRoom->name }}</option>
                                @endforeach
                            </select>

                            <select name="student_id" class="auth-input auth-select">
                                <option value="">Select student</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}" @selected($selectedStudent && (int) $selectedStudent->id === (int) $student->id)>{{ $student->name }}</option>
                                @endforeach
                            </select>

                            <button type="submit" class="primary-action">Open Report</button>
                        </form>

                        <div class="border-t border-slate-200/70 pt-5 mt-5">
                            <h4 class="section-heading text-base">What You’ll See</h4>
                            <div class="dashboard-list mt-4">
                                <div class="dashboard-list-item">
                                    <div class="signal-row">
                                        <span class="inline-flex items-center gap-2"><span class="signal-dot bg-emerald-500"></span>Attendance signals</span>
                                        <span class="data-pill">Tracked</span>
                                    </div>
                                </div>
                                <div class="dashboard-list-item">
                                    <div class="signal-row">
                                        <span class="inline-flex items-center gap-2"><span class="signal-dot bg-blue-500"></span>Exam results</span>
                                        <span class="data-pill">Published</span>
                                    </div>
                                </div>
                                <div class="dashboard-list-item">
                                    <div class="signal-row">
                                        <span class="inline-flex items-center gap-2"><span class="signal-dot bg-violet-500"></span>Assignment scores</span>
                                        <span class="data-pill">Graded</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($selectedStudent && $report)
                <div class="dashboard-cluster">
                    <div class="spotlight-panel">
                        <p class="eyebrow !text-slate-500">Selected learner</p>
                        <h3 class="mt-3 text-3xl font-semibold text-slate-900">{{ $selectedStudent->name }}</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-500">A focused report across subject progress, exams, assignments, and attendance for this student.</p>

                        <div class="highlight-metrics !mt-5 !xl:grid-cols-2">
                            <div class="highlight-metric-soft">
                                <p class="highlight-metric-label">Overall</p>
                                <p class="highlight-metric-value">{{ $selectedOverview['overall_percentage'] === null ? 'No data' : number_format($selectedOverview['overall_percentage'], 1).'%' }}</p>
                                <p class="highlight-metric-copy">Blended progress view.</p>
                            </div>
                            <div class="highlight-metric-soft">
                                <p class="highlight-metric-label">Attendance</p>
                                <p class="highlight-metric-value">{{ number_format($selectedAttendance['percentage'], 1) }}%</p>
                                <p class="highlight-metric-copy">{{ $selectedAttendance['total'] }} attendance records.</p>
                            </div>
                            <div class="highlight-metric-soft">
                                <p class="highlight-metric-label">Exams</p>
                                <p class="highlight-metric-value">{{ $selectedExams['count'] }}</p>
                                <p class="highlight-metric-copy">Published exam results.</p>
                            </div>
                            <div class="highlight-metric-soft">
                                <p class="highlight-metric-label">Assignments</p>
                                <p class="highlight-metric-value">{{ $selectedAssignments['graded'] }}</p>
                                <p class="highlight-metric-copy">Graded submissions.</p>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-rail">
                        <div class="spotlight-panel">
                            <h3 class="section-heading">Report Scope</h3>
                            <div class="mt-5 space-y-4">
                                <div class="metric-row">
                                    <div>
                                        <p class="font-semibold text-slate-900">Classes</p>
                                        <p class="text-sm text-slate-500">Learning groups in this report</p>
                                    </div>
                                    <span class="data-pill">{{ $selectedOverview['classes_count'] }}</span>
                                </div>
                                <div class="metric-row">
                                    <div>
                                        <p class="font-semibold text-slate-900">Subjects</p>
                                        <p class="text-sm text-slate-500">Subject areas contributing to the trend</p>
                                    </div>
                                    <span class="data-pill">{{ $selectedOverview['subjects_count'] }}</span>
                                </div>
                                <div class="metric-row">
                                    <div>
                                        <p class="font-semibold text-slate-900">Teachers</p>
                                        <p class="text-sm text-slate-500">Current instructional touchpoints</p>
                                    </div>
                                    <span class="data-pill">{{ $selectedOverview['teachers_count'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('reports.partials.student-performance', ['student' => $selectedStudent, 'report' => $report])
            @else
                <div class="spotlight-panel text-sm text-slate-500">
                    Choose a student to open the premium report view.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
