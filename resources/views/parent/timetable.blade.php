<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">Children Timetable</h2>
                <p class="module-copy">Class schedules for each linked child, including teacher and location details.</p>
            </div>

            <a href="{{ route('parent.dashboard') }}" class="secondary-action">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell max-w-6xl">
            <div class="space-y-6">
                @forelse ($children as $child)
                    <div class="module-card">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">{{ $child->name }}</h3>
                                <p class="mt-1 text-sm text-slate-500">Weekly schedule across all enrolled classes.</p>
                            </div>
                            <span class="data-pill">{{ $child->enrolledClasses->flatMap->scheduleEntries->count() }} entries</span>
                        </div>

                        <div class="mt-5 space-y-3">
                            @forelse ($child->enrolledClasses->flatMap->scheduleEntries->sortBy('starts_at') as $entry)
                                <div class="module-subcard">
                                    <p class="font-semibold text-slate-900">
                                        {{ $entry->day_of_week }}
                                        -
                                        {{ substr($entry->starts_at, 0, 5) }}-{{ substr($entry->ends_at, 0, 5) }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $entry->classRoom->name }}
                                        -
                                        {{ $entry->subject->name }}
                                        -
                                        {{ $entry->teacher->name }}
                                        @if ($entry->location)
                                            - {{ $entry->location }}
                                        @endif
                                    </p>
                                </div>
                            @empty
                                <div class="module-subcard text-sm text-slate-500">
                                    No timetable entries for this child yet.
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
    </div>
</x-app-layout>
