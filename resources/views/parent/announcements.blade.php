<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">Announcements</h2>
                <p class="module-copy">Updates that matter to the children linked to your account.</p>
            </div>

            <a href="{{ route('parent.dashboard') }}" class="secondary-action">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell max-w-5xl">
            <div class="space-y-4">
                @forelse ($announcements as $announcement)
                    <div class="module-card">
                        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">{{ $announcement->title }}</h3>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $announcement->classRoom?->name ?? 'Whole school' }}
                                    -
                                    Posted by {{ $announcement->creator->name }}
                                </p>
                            </div>
                            <span class="data-pill">{{ $announcement->created_at->format('d M Y') }}</span>
                        </div>

                        <p class="mt-4 text-sm leading-6 text-slate-600">{{ $announcement->body }}</p>
                    </div>
                @empty
                    <div class="module-card text-sm text-slate-500">
                        No announcements are available right now.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
