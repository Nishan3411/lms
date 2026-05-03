<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="module-title">Announcements</h2>
            <p class="module-copy">Send updates to students or parents in your assigned classes.</p>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell">
            @if (session('success'))
                <div class="notice-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="notice-error">
                    <ul class="list-disc ps-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="module-card">
                <h3 class="text-lg font-semibold text-slate-900">New Announcement</h3>
                <form method="POST" action="{{ route('teacher.announcements.store') }}" class="mt-5 grid gap-4 lg:grid-cols-2">
                    @csrf
                    <input type="text" name="title" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Announcement title" required>
                    <select name="class_room_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Select class</option>
                        @foreach ($classRooms as $classRoom)
                            <option value="{{ $classRoom->id }}">{{ $classRoom->name }}</option>
                        @endforeach
                    </select>
                    <select name="audience" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 lg:col-span-2" required>
                        <option value="students">Students</option>
                        <option value="parents">Parents</option>
                    </select>
                    <textarea name="body" rows="4" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 lg:col-span-2" placeholder="Write announcement..." required></textarea>
                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 lg:col-span-2">Publish</button>
                </form>
            </div>

            <div class="space-y-4">
                @forelse ($announcements as $announcement)
                    <div class="module-card">
                        <h3 class="font-semibold text-slate-900">{{ $announcement->title }}</h3>
                        <p class="text-sm text-slate-500">{{ ucfirst($announcement->audience) }} - {{ $announcement->classRoom->name }}</p>
                        <p class="mt-3 text-sm text-slate-600">{{ $announcement->body }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">You have not published announcements yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
