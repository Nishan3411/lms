<x-app-layout>
    @php
        $publishedCount = $exams->where(fn ($exam) => $exam->isPublished())->count();
        $draftCount = $exams->count() - $publishedCount;
        $resultCount = $exams->sum(fn ($exam) => $exam->results->count());
    @endphp

    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">Result Reports</h2>
                <p class="module-copy">Review exam schedules, publication status, and student marks across classes.</p>
            </div>

            <a href="{{ route('admin.dashboard') }}" class="secondary-action">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell">
            <div class="dashboard-cluster">
                <div class="spotlight-panel-ink">
                    <div class="relative z-10">
                        <p class="eyebrow">Results command view</p>
                        <h3 class="spotlight-title mt-3">Track exam publishing, result coverage, and marks across your academic structure.</h3>
                        <p class="spotlight-copy">Filter the report to a class, then review which exams are published and how results are distributed.</p>

                        <div class="highlight-metrics">
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Exams</p>
                                <p class="highlight-metric-value">{{ $exams->count() }}</p>
                                <p class="highlight-metric-copy">Exam records in the current report.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Published</p>
                                <p class="highlight-metric-value">{{ $publishedCount }}</p>
                                <p class="highlight-metric-copy">Ready for student and parent view.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Draft</p>
                                <p class="highlight-metric-value">{{ $draftCount }}</p>
                                <p class="highlight-metric-copy">Still pending publication.</p>
                            </div>
                            <div class="highlight-metric">
                                <p class="highlight-metric-label">Results</p>
                                <p class="highlight-metric-value">{{ $resultCount }}</p>
                                <p class="highlight-metric-copy">Student result rows inside the report.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-rail">
                    <div class="spotlight-panel">
                        <h3 class="section-heading">Filter Exams</h3>
                        <p class="section-copy">Choose a class to focus on a smaller result set.</p>

                        <form method="GET" action="{{ route('admin.results.index') }}" class="filter-grid-compact">
                            <div class="auth-field">
                                <label for="class_room_id" class="auth-label">Class</label>
                                <select id="class_room_id" name="class_room_id" class="auth-input auth-select">
                                    <option value="">All classes</option>
                                    @foreach ($classRooms as $classRoom)
                                        <option value="{{ $classRoom->id }}" @selected((string) $selectedClassRoomId === (string) $classRoom->id)>{{ $classRoom->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex items-end">
                                <button type="submit" class="primary-action w-full">Apply Filter</button>
                            </div>
                        </form>
                    </div>

                    <div class="spotlight-panel">
                        <h3 class="section-heading">Scope</h3>
                        <div class="summary-grid !mt-5 !xl:grid-cols-2">
                            <div class="summary-card-soft">
                                <p class="summary-card-soft-label">Classes</p>
                                <p class="summary-card-soft-value">{{ $classRooms->count() }}</p>
                                <p class="summary-card-soft-copy">Available to filter.</p>
                            </div>
                            <div class="summary-card-soft">
                                <p class="summary-card-soft-label">Visible Exams</p>
                                <p class="summary-card-soft-value">{{ $exams->count() }}</p>
                                <p class="summary-card-soft-copy">Shown in the current report.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-5">
                @forelse ($exams as $exam)
                    <div class="overview-slab">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <h3 class="text-xl font-semibold text-slate-900">{{ $exam->title }}</h3>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $exam->classRoom->name }} / {{ $exam->subject->name }} / {{ $exam->teacher->name }} / {{ $exam->exam_date->format('d M Y') }}
                                </p>
                            </div>
                            <span class="state-chip {{ $exam->isPublished() ? 'state-chip-published' : 'state-chip-draft' }}">
                                {{ $exam->isPublished() ? 'Published' : 'Draft' }}
                            </span>
                        </div>

                        <div class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                            @foreach ($exam->results as $result)
                                <div class="module-subcard">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="font-medium text-slate-900">{{ $result->student->name }}</span>
                                        <span class="state-chip state-chip-neutral">{{ $result->grade }}</span>
                                    </div>
                                    <p class="mt-2 text-sm text-slate-600">{{ number_format($result->marks_obtained, 2) }} / {{ $exam->max_marks }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="spotlight-panel text-sm text-slate-500">
                        No exams found for the selected filter.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
