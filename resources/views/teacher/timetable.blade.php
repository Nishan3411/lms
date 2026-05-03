<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="module-title">My Timetable</h2>
            <p class="module-copy">Your class-wise teaching schedule.</p>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="mx-auto max-w-6xl space-y-3 sm:px-6 lg:px-8">
            @forelse ($scheduleEntries as $entry)
                <div class="module-card !p-5">
                    <p class="font-semibold text-slate-900">{{ $entry->day_of_week }} - {{ substr($entry->starts_at, 0, 5) }}-{{ substr($entry->ends_at, 0, 5) }}</p>
                    <p class="text-sm text-slate-500">{{ $entry->classRoom->name }} - {{ $entry->subject->name }}{{ $entry->location ? ' - '.$entry->location : '' }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-500">No timetable entries assigned yet.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
