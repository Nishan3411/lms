<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="module-title">Attendance</h2>
            <p class="module-copy">Mark daily attendance for your assigned classes and subjects.</p>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="mx-auto max-w-6xl space-y-6 sm:px-6 lg:px-8">
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
                <form method="GET" action="{{ route('teacher.attendance.mark') }}" class="grid gap-4 md:grid-cols-[1fr_1fr_220px_auto]">
                    <select name="class_room_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Select class</option>
                        @foreach ($classRooms as $classRoom)
                            <option value="{{ $classRoom->id }}" @selected((string) request('class_room_id', optional($selectedClass)->id) === (string) $classRoom->id)>{{ $classRoom->name }}</option>
                        @endforeach
                    </select>

                    @php
                        $availableSubjects = $selectedClass
                            ? $selectedClass->subjects
                            : $classRooms->flatMap->subjects->sortBy('name')->values();
                    @endphp

                    <select name="subject_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Select subject</option>
                        @foreach ($availableSubjects as $subject)
                            <option value="{{ $subject->id }}" @selected((string) request('subject_id', optional($selectedSubject)->id) === (string) $subject->id)>{{ $subject->name }}</option>
                        @endforeach
                    </select>

                    <input type="date" name="date" value="{{ $selectedDate }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Load Subject</button>
                </form>
            </div>

            @if ($selectedClass && $selectedSubject)
                <div class="module-card">
                    <h3 class="text-lg font-semibold text-slate-900">{{ $selectedClass->name }} - {{ $selectedSubject->name }} - {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</h3>
                    <form method="POST" action="{{ route('teacher.attendance.store') }}" class="mt-5 space-y-4">
                        @csrf
                        <input type="hidden" name="class_room_id" value="{{ $selectedClass->id }}">
                        <input type="hidden" name="subject_id" value="{{ $selectedSubject->id }}">
                        <input type="hidden" name="date" value="{{ $selectedDate }}">

                        @foreach ($selectedClass->students as $index => $student)
                            <div class="module-subcard">
                                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $student->name }}</p>
                                        <input type="hidden" name="records[{{ $index }}][student_id]" value="{{ $student->id }}">
                                    </div>
                                    <div class="flex gap-3">
                                        @foreach (['present' => 'Present', 'absent' => 'Absent', 'late' => 'Late'] as $value => $label)
                                            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                                <input type="radio" name="records[{{ $index }}][status]" value="{{ $value }}" @checked(old("records.$index.status", 'present') === $value)>
                                                {{ $label }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Save Attendance</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
