<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="module-title">Timetable Management</h2>
            <p class="module-copy">Create class-wise and teacher-wise schedule entries.</p>
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
                <h3 class="text-lg font-semibold text-slate-900">Add Schedule Entry</h3>
                <form method="POST" action="{{ route('admin.timetable.store') }}" class="mt-5 grid gap-4 lg:grid-cols-3">
                    @csrf
                    <select name="class_room_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Select class</option>
                        @foreach ($classRooms as $classRoom)
                            <option value="{{ $classRoom->id }}">{{ $classRoom->name }}</option>
                        @endforeach
                    </select>
                    <select name="subject_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Select subject</option>
                        @foreach ($classRooms as $classRoom)
                            @foreach ($classRoom->subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $classRoom->name }})</option>
                            @endforeach
                        @endforeach
                    </select>
                    <select name="teacher_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Select teacher</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                    <select name="day_of_week" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                            <option value="{{ $day }}">{{ $day }}</option>
                        @endforeach
                    </select>
                    <input type="time" name="starts_at" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    <input type="time" name="ends_at" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    <input type="text" name="location" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 lg:col-span-2" placeholder="Room 101">
                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Add Entry</button>
                </form>
            </div>

            <div class="module-card">
                <h3 class="text-lg font-semibold text-slate-900">Schedule</h3>
                <div class="mt-5 space-y-3">
                    @forelse ($scheduleEntries as $entry)
                        <div class="module-subcard">
                            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $entry->day_of_week }} · {{ substr($entry->starts_at, 0, 5) }}-{{ substr($entry->ends_at, 0, 5) }}</p>
                                    <p class="text-sm text-slate-500">{{ $entry->classRoom->name }} · {{ $entry->subject->name }} · {{ $entry->teacher->name }}{{ $entry->location ? ' · '.$entry->location : '' }}</p>
                                </div>
                                <form method="POST" action="{{ route('admin.timetable.destroy', $entry) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700">Remove</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No schedule entries yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
