<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">Exams & Results</h2>
                <p class="module-copy">Schedule exams, enter marks, and publish results.</p>
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
                <h3 class="text-lg font-semibold text-slate-900">Schedule Exam</h3>
                <form method="POST" action="{{ route('teacher.exams.store') }}" class="mt-5 grid gap-4 lg:grid-cols-2">
                    @csrf
                    <input type="text" name="title" value="{{ old('title') }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Mid Semester Exam" required>
                    <select name="class_room_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Select class</option>
                        @foreach ($classRooms as $classRoom)
                            <option value="{{ $classRoom->id }}" @selected((string) old('class_room_id') === (string) $classRoom->id)>{{ $classRoom->name }}</option>
                        @endforeach
                    </select>
                    <select name="subject_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Select subject</option>
                        @foreach ($classRooms as $classRoom)
                            @foreach ($classRoom->subjects as $subject)
                                <option value="{{ $subject->id }}" @selected((string) old('subject_id') === (string) $subject->id)>{{ $subject->name }} ({{ $classRoom->name }})</option>
                            @endforeach
                        @endforeach
                    </select>
                    <input type="date" name="exam_date" value="{{ old('exam_date') }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    <input type="number" name="max_marks" min="1" max="1000" value="{{ old('max_marks', 100) }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Max marks" required>
                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                        Schedule Exam
                    </button>
                </form>
            </div>

            <div class="space-y-5">
                @forelse ($exams as $exam)
                    <div class="module-card">
                        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">{{ $exam->title }}</h3>
                                <p class="mt-1 text-sm text-slate-500">{{ $exam->classRoom->name }} · {{ $exam->subject->name }} · {{ $exam->exam_date->format('d M Y') }}</p>
                                <p class="mt-2 text-sm text-slate-600">Max marks: {{ $exam->max_marks }} · Status: {{ $exam->isPublished() ? 'Published' : 'Draft' }}</p>
                            </div>
                            @if (! $exam->isPublished())
                                <form method="POST" action="{{ route('teacher.exams.publish', $exam) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                                        Publish Results
                                    </button>
                                </form>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('teacher.exams.results.store', $exam) }}" class="mt-5 space-y-3">
                            @csrf
                            @foreach ($exam->classRoom->students as $index => $student)
                                @php $result = $exam->results->firstWhere('student_id', $student->id); @endphp
                                <div class="grid gap-3 rounded-[22px] bg-slate-50/90 p-4 ring-1 ring-slate-200/70 md:grid-cols-[1fr_160px_1fr]">
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $student->name }}</p>
                                        <input type="hidden" name="results[{{ $index }}][student_id]" value="{{ $student->id }}">
                                    </div>
                                    <input type="number" step="0.01" min="0" max="{{ $exam->max_marks }}" name="results[{{ $index }}][marks_obtained]" value="{{ old("results.$index.marks_obtained", $result?->marks_obtained) }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Marks">
                                    <input type="text" name="results[{{ $index }}][remarks]" value="{{ old("results.$index.remarks", $result?->remarks) }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Remarks">
                                </div>
                            @endforeach

                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                                Save Marks
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="module-card text-sm text-slate-500">No exams scheduled yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
