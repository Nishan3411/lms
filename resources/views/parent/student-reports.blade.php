<x-app-layout>
    @php
        $averageOverall = collect($reports)
            ->pluck('report')
            ->pluck('overview')
            ->pluck('overall_percentage')
            ->filter(fn ($value) => $value !== null)
            ->avg();

        $averageAttendance = collect($reports)
            ->pluck('report')
            ->pluck('attendance')
            ->pluck('summary')
            ->pluck('percentage')
            ->filter(fn ($value) => $value !== null)
            ->avg();
    @endphp

    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">Child Performance Reports</h2>
                <p class="module-copy">A premium report view for every linked child, all from one parent portal.</p>
            </div>

            <a href="{{ route('parent.dashboard') }}" class="secondary-action">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell">
            <div class="dashboard-cluster">
                <div class="spotlight-panel-ink">
                    <div class="relative z-10">
                        <p class="eyebrow">Family report center</p>
                        <h3 class="spotlight-title mt-3">See each child’s academic picture clearly, from attendance to graded work.</h3>
                        <p class="spotlight-copy">Everything here is organized so you can review progress, spot risks early, and stay connected to day-to-day learning.</p>

                        <div class="highlight-metrics">
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Children</p>
                                <p class="highlight-metric-value">{{ count($reports) }}</p>
                                <p class="highlight-metric-copy">Student reports linked to this account.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Average overall</p>
                                <p class="highlight-metric-value">{{ $averageOverall === null ? 'No data' : number_format($averageOverall, 1).'%' }}</p>
                                <p class="highlight-metric-copy">Combined progress across linked children.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Average attendance</p>
                                <p class="highlight-metric-value">{{ $averageAttendance === null ? 'No data' : number_format($averageAttendance, 1).'%' }}</p>
                                <p class="highlight-metric-copy">Attendance health across child reports.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Report depth</p>
                                <p class="highlight-metric-value">{{ collect($reports)->sum(fn ($entry) => $entry['report']['overview']['subjects_count']) }}</p>
                                <p class="highlight-metric-copy">Subjects being tracked right now.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-rail">
                    <div class="spotlight-panel">
                        <h3 class="section-heading">Children in View</h3>
                        <div class="dashboard-list mt-5">
                            @forelse ($reports as $entry)
                                <div class="dashboard-list-item">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ $entry['student']->name }}</p>
                                            <p class="text-sm text-slate-500">{{ $entry['report']['overview']['subjects_count'] }} subjects in report</p>
                                        </div>
                                        <span class="data-pill">{{ $entry['report']['overview']['overall_percentage'] === null ? 'No data' : number_format($entry['report']['overview']['overall_percentage'], 1).'%' }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">No linked children yet.</p>
                            @endforelse
                        </div>

                        <div class="border-t border-slate-200/70 pt-5 mt-5">
                            <h4 class="section-heading text-base">Inside Each Report</h4>
                            <div class="dashboard-list mt-4">
                                <div class="dashboard-list-item">
                                    <div class="signal-row">
                                        <span class="inline-flex items-center gap-2"><span class="signal-dot bg-emerald-500"></span>Attendance trend</span>
                                        <span class="data-pill">Visual</span>
                                    </div>
                                </div>
                                <div class="dashboard-list-item">
                                    <div class="signal-row">
                                        <span class="inline-flex items-center gap-2"><span class="signal-dot bg-blue-500"></span>Exam performance</span>
                                        <span class="data-pill">Published</span>
                                    </div>
                                </div>
                                <div class="dashboard-list-item">
                                    <div class="signal-row">
                                        <span class="inline-flex items-center gap-2"><span class="signal-dot bg-violet-500"></span>Assignment results</span>
                                        <span class="data-pill">Graded</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @forelse ($reports as $entry)
                <section id="student-report-{{ $entry['student']->id }}" class="space-y-6">
                    <div class="dashboard-cluster">
                        <div class="spotlight-panel">
                            <p class="eyebrow !text-slate-500">Child report</p>
                            <h3 class="mt-3 text-3xl font-semibold text-slate-900">{{ $entry['student']->name }}</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-500">Review attendance, exam marks, assignment performance, and subject-level progress for this child in one place.</p>

                            <div class="highlight-metrics !mt-5 !xl:grid-cols-2">
                                <div class="highlight-metric-soft">
                                    <p class="highlight-metric-label">Overall</p>
                                    <p class="highlight-metric-value">{{ $entry['report']['overview']['overall_percentage'] === null ? 'No data' : number_format($entry['report']['overview']['overall_percentage'], 1).'%' }}</p>
                                    <p class="highlight-metric-copy">Combined academic performance.</p>
                                </div>
                                <div class="highlight-metric-soft">
                                    <p class="highlight-metric-label">Attendance</p>
                                    <p class="highlight-metric-value">{{ number_format($entry['report']['attendance']['summary']['percentage'], 1) }}%</p>
                                    <p class="highlight-metric-copy">{{ $entry['report']['attendance']['summary']['total'] }} records.</p>
                                </div>
                                <div class="highlight-metric-soft">
                                    <p class="highlight-metric-label">Exams</p>
                                    <p class="highlight-metric-value">{{ $entry['report']['exams']['summary']['count'] }}</p>
                                    <p class="highlight-metric-copy">Published exam results.</p>
                                </div>
                                <div class="highlight-metric-soft">
                                    <p class="highlight-metric-label">Assignments</p>
                                    <p class="highlight-metric-value">{{ $entry['report']['assignments']['summary']['graded'] }}</p>
                                    <p class="highlight-metric-copy">Graded assignment submissions.</p>
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
                                            <p class="text-sm text-slate-500">Classes represented here</p>
                                        </div>
                                        <span class="data-pill">{{ $entry['report']['overview']['classes_count'] }}</span>
                                    </div>
                                    <div class="metric-row">
                                        <div>
                                            <p class="font-semibold text-slate-900">Subjects</p>
                                            <p class="text-sm text-slate-500">Academic areas being tracked</p>
                                        </div>
                                        <span class="data-pill">{{ $entry['report']['overview']['subjects_count'] }}</span>
                                    </div>
                                    <div class="metric-row">
                                        <div>
                                            <p class="font-semibold text-slate-900">Teachers</p>
                                            <p class="text-sm text-slate-500">Teachers connected to this report</p>
                                        </div>
                                        <span class="data-pill">{{ $entry['report']['overview']['teachers_count'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 

                    @include('reports.partials.student-performance', ['student' => $entry['student'], 'report' => $entry['report']])
                </section>
            @empty
                <div class="spotlight-panel text-sm text-slate-500">
                    No children are linked to this parent account yet.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
