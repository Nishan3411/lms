<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="module-title">Announcements</h2>
            <p class="module-copy">Updates from your school and teachers.</p>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="mx-auto max-w-5xl space-y-4 sm:px-6 lg:px-8">
            @forelse ($announcements as $announcement)
                <div class="module-card">
                    <h3 class="font-semibold text-slate-900">{{ $announcement->title }}</h3>
                    <p class="text-sm text-slate-500">{{ $announcement->classRoom?->name ?? 'Whole school' }} - {{ $announcement->creator->name }}</p>
                    <p class="mt-3 text-sm text-slate-600">{{ $announcement->body }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-500">No announcements available.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
