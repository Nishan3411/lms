<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">Assignments</h2>
                <p class="module-copy">Create assignments and review student submissions.</p>
            </div>
            <a href="{{ route('teacher.dashboard') }}" class="secondary-action">
                Back to Dashboard
            </a>
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

            <div class="module-card">
                <h3 class="text-lg font-semibold text-slate-900">Create Assignment</h3>
                <p class="text-sm text-slate-500">Students enrolled in the selected class will see this assignment.</p>

                <form method="POST" action="{{ route('teacher.assignments.store') }}" enctype="multipart/form-data" class="mt-5 grid gap-4 lg:grid-cols-2">
                    @csrf

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <input id="title" name="title" type="text" value="{{ old('title') }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>

                    <div>
                        <label for="class_room_id" class="block text-sm font-medium text-gray-700">Class</label>
                        <select id="class_room_id" name="class_room_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Select class</option>
                            @foreach ($classRooms as $classRoom)
                                <option value="{{ $classRoom->id }}" @selected((string) old('class_room_id') === (string) $classRoom->id)>{{ $classRoom->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject</label>
                        <select id="subject_id" name="subject_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">General class assignment</option>
                            @foreach ($classRooms as $classRoom)
                                @foreach ($classRoom->subjects as $subject)
                                    <option value="{{ $subject->id }}" @selected((string) old('subject_id') === (string) $subject->id)>
                                        {{ $subject->name }} ({{ $classRoom->name }})
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="max_marks" class="block text-sm font-medium text-gray-700">Max Marks</label>
                        <input id="max_marks" name="max_marks" type="number" min="1" max="1000" value="{{ old('max_marks', 100) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>

                    <div>
                        <label for="due_at" class="block text-sm font-medium text-gray-700">Due Date</label>
                        <input id="due_at" name="due_at" type="datetime-local" value="{{ old('due_at') }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>

                    <div>
                        <label for="attachment" class="block text-sm font-medium text-gray-700">Attachment</label>
                        <input id="attachment" name="attachment" type="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.zip" class="mt-1 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">Optional. Allowed: PDF, DOC, DOCX, PPT, PPTX, ZIP. Max 20 MB.</p>
                    </div>

                    <div class="lg:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Instructions</label>
                        <textarea id="description" name="description" rows="4" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                    </div>

                    <div class="lg:col-span-2">
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                            Create Assignment
                        </button>
                    </div>
                </form>
            </div>

            <div class="space-y-5">
                @forelse ($assignments as $assignment)
                    <div class="module-card">
                        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">{{ $assignment->title }}</h3>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $assignment->classRoom->name }}
                                    @if ($assignment->subject)
                                        · {{ $assignment->subject->name }}
                                    @endif
                                    · Due {{ $assignment->due_at->format('d M Y, h:i A') }}
                                </p>
                                <p class="mt-2 text-sm text-slate-600">{{ $assignment->description ?: 'No extra instructions.' }}</p>
                                <p class="mt-2 text-xs text-slate-500">Max marks: {{ $assignment->max_marks }}</p>
                                @if ($assignment->attachment_path)
                                    <a href="{{ route('teacher.assignments.download', $assignment) }}" class="mt-2 inline-flex text-sm font-medium text-slate-700 underline">
                                        Download assignment attachment
                                    </a>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('teacher.assignments.destroy', $assignment) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700">Delete</button>
                            </form>
                        </div>

                        <div class="mt-5">
                            <h4 class="font-semibold text-slate-900">Submissions</h4>
                            <div class="mt-3 space-y-3">
                                @forelse ($assignment->submissions as $submission)
                                    <div class="module-subcard">
                                        <div class="grid gap-4 lg:grid-cols-[1fr_280px]">
                                            <div>
                                                <p class="font-medium text-slate-900">{{ $submission->student->name }}</p>
                                                <p class="text-sm text-slate-500">
                                                    {{ ucfirst($submission->status) }}
                                                    @if ($submission->submitted_at)
                                                        · Submitted {{ $submission->submitted_at->format('d M Y, h:i A') }}
                                                    @endif
                                                </p>
                                                @if ($submission->answer_text)
                                                    <p class="mt-2 text-sm text-slate-600">{{ $submission->answer_text }}</p>
                                                @endif
                                                @if ($submission->file_path)
                                                    <a href="{{ route('teacher.assignment-submissions.download', $submission) }}" class="mt-2 inline-flex text-sm font-medium text-slate-700 underline">
                                                        Download submitted file
                                                    </a>
                                                @endif
                                                @if ($submission->status === 'graded')
                                                    <p class="mt-2 text-sm font-semibold text-slate-900">Marks: {{ number_format($submission->marks_obtained, 2) }} / {{ $assignment->max_marks }}</p>
                                                    <p class="text-sm text-slate-600">{{ $submission->teacher_feedback ?: 'No feedback provided.' }}</p>
                                                @endif
                                            </div>

                                            <form method="POST" action="{{ route('teacher.assignment-submissions.grade', $submission) }}" class="space-y-3">
                                                @csrf
                                                @method('PATCH')
                                                <input type="number" step="0.01" min="0" max="{{ $assignment->max_marks }}" name="marks_obtained" value="{{ old('marks_obtained', $submission->marks_obtained) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Marks" required>
                                                <textarea name="teacher_feedback" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Feedback">{{ old('teacher_feedback', $submission->teacher_feedback) }}</textarea>
                                                <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                                                    Save Review
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-slate-500">No students have submitted this assignment yet.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="module-card text-sm text-slate-500">
                        You have not created any assignments yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
