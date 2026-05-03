<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">Children Results</h2>
                <p class="module-copy">Published exam results and performance summaries for linked children.</p>
            </div>

            <a href="{{ route('parent.dashboard') }}" class="secondary-action">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell max-w-6xl">
            @forelse ($children as $child)
                <div class="module-card">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ $child->name }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $child->result_summary['count'] }} published result records.</p>
                        </div>
                        <p class="text-3xl font-semibold text-slate-900">{{ number_format($child->result_summary['percentage'], 2) }}%</p>
                    </div>

                    <div class="mt-5 space-y-3">
                        @forelse ($child->examResults as $result)
                            <div class="module-subcard">
                                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                    <div>
                                        <h4 class="font-semibold text-slate-900">{{ $result->exam->title }}</h4>
                                        <p class="text-sm text-slate-500">
                                            {{ $result->exam->classRoom->name }}
                                            -
                                            {{ $result->exam->subject->name }}
                                            -
                                            {{ $result->exam->exam_date->format('d M Y') }}
                                        </p>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-900">
                                        {{ number_format($result->marks_obtained, 2) }} / {{ $result->exam->max_marks }}
                                        -
                                        {{ $result->grade }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="module-subcard text-sm text-slate-500">
                                No published results for this child yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="module-card text-sm text-slate-500">
                    No children are linked to this account yet.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
