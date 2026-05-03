<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">My Results</h2>
                <p class="module-copy">Published exam results and a running view of your academic performance.</p>
            </div>

            <a href="{{ route('student.dashboard') }}" class="secondary-action">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell max-w-6xl">
            <div class="dashboard-cluster">
                <div class="spotlight-panel-ink">
                    <div class="relative z-10">
                        <p class="eyebrow">Result overview</p>
                        <h3 class="spotlight-title mt-3">Keep a clean running view of your marks, grades, and published exam performance.</h3>
                        <p class="spotlight-copy">Every published exam result stays in one place so you can see both the headline trend and the detailed mark history.</p>

                        <div class="highlight-metrics">
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Overall</p>
                                <p class="highlight-metric-value">{{ number_format($summary['percentage'], 1) }}%</p>
                                <p class="highlight-metric-copy">Average across published results.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Published</p>
                                <p class="highlight-metric-value">{{ $summary['count'] }}</p>
                                <p class="highlight-metric-copy">Result records available to review.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-rail">
                    <div class="spotlight-panel">
                        <h3 class="section-heading">Performance Snapshot</h3>
                        <div class="summary-grid !mt-5 !xl:grid-cols-2">
                            <div class="summary-card-soft">
                                <p class="summary-card-soft-label">Results</p>
                                <p class="summary-card-soft-value">{{ $results->count() }}</p>
                                <p class="summary-card-soft-copy">Published exam entries.</p>
                            </div>
                            <div class="summary-card-soft">
                                <p class="summary-card-soft-label">Latest grade</p>
                                <p class="summary-card-soft-value">{{ optional($results->first())->grade ?? 'Soon' }}</p>
                                <p class="summary-card-soft-copy">Most recent published result.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                @forelse ($results as $result)
                    <div class="overview-slab">
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h3 class="text-xl font-semibold text-slate-900">{{ $result->exam->title }}</h3>
                                <p class="text-sm text-slate-500">{{ $result->exam->classRoom->name }} / {{ $result->exam->subject->name }} / {{ $result->exam->exam_date->format('d M Y') }}</p>
                                <p class="mt-2 text-sm text-slate-600">{{ $result->remarks ?: 'No remarks shared for this result yet.' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-semibold text-slate-900">{{ number_format($result->marks_obtained, 2) }} / {{ $result->exam->max_marks }}</p>
                                <span class="state-chip state-chip-neutral mt-2">{{ $result->grade }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="spotlight-panel text-sm text-slate-500">
                        No published results are available yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
