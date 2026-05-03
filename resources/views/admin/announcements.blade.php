<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">Announcements</h2>
                <p class="module-copy">Publish school-wide updates or target a specific audience and class.</p>
            </div>

            <a href="{{ route('admin.dashboard') }}" class="secondary-action">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell">
            @if (session('success'))
                <div class="notice-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="notice-error">
                    <p class="font-semibold">Please fix the following issues:</p>
                    <ul class="mt-2 list-disc ps-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid gap-6 xl:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]">
                <div class="module-card">
                    <h3 class="text-lg font-semibold text-slate-900">New Announcement</h3>
                    <p class="mt-1 text-sm text-slate-500">Use this to keep students, parents, and teachers updated.</p>

                    <form method="POST" action="{{ route('admin.announcements.store') }}" class="mt-5 grid gap-4">
                        @csrf

                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input
                                id="title"
                                type="text"
                                name="title"
                                value="{{ old('title') }}"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Announcement title"
                                required
                            >
                        </div>

                        <div>
                            <label for="audience" class="block text-sm font-medium text-gray-700">Audience</label>
                            <select id="audience" name="audience" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="all" @selected(old('audience') === 'all')>Everyone</option>
                                <option value="students" @selected(old('audience') === 'students')>Students</option>
                                <option value="parents" @selected(old('audience') === 'parents')>Parents</option>
                                <option value="teachers" @selected(old('audience') === 'teachers')>Teachers</option>
                            </select>
                        </div>

                        <div>
                            <label for="class_room_id" class="block text-sm font-medium text-gray-700">Class Scope</label>
                            <select id="class_room_id" name="class_room_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Whole school</option>
                                @foreach ($classRooms as $classRoom)
                                    <option value="{{ $classRoom->id }}" @selected((string) old('class_room_id') === (string) $classRoom->id)>{{ $classRoom->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="body" class="block text-sm font-medium text-gray-700">Message</label>
                            <textarea
                                id="body"
                                name="body"
                                rows="5"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Write announcement..."
                                required
                            >{{ old('body') }}</textarea>
                        </div>

                        <button type="submit" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                            Publish Announcement
                        </button>
                    </form>
                </div>

                <div class="module-card">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="section-heading">Recent Announcements</h3>
                            <p class="section-copy">Published updates appear here in newest-first order.</p>
                        </div>
                        <span class="data-pill">{{ $announcements->count() }} items</span>
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse ($announcements as $announcement)
                            <div class="module-subcard">
                                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                    <div>
                                        <h4 class="font-semibold text-slate-900">{{ $announcement->title }}</h4>
                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ ucfirst($announcement->audience) }}
                                            -
                                            {{ $announcement->classRoom?->name ?? 'Whole school' }}
                                            -
                                            Posted by {{ $announcement->creator->name }}
                                        </p>
                                    </div>
                                    <span class="data-pill">{{ $announcement->created_at->format('d M Y') }}</span>
                                </div>

                                <p class="mt-3 text-sm leading-6 text-slate-600">{{ $announcement->body }}</p>
                            </div>
                        @empty
                            <div class="module-subcard text-sm text-slate-500">
                                No announcements have been published yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
