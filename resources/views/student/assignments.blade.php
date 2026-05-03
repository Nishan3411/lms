<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">Assignments</h2>
                <p class="module-copy">Submit work and review teacher feedback.</p>
            </div>
            <a href="{{ route('student.dashboard') }}" class="secondary-action">Back to Dashboard</a>
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

            <div class="space-y-5">
                @forelse ($assignments as $assignment)
                    @php $submission = $assignment->submissions->first(); @endphp
                    <div class="module-card">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">{{ $assignment->title }}</h3>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $assignment->classRoom->name }}
                                    @if ($assignment->subject)
                                        - {{ $assignment->subject->name }}
                                    @endif
                                    - {{ $assignment->teacher->name }}
                                    - Due {{ $assignment->due_at->format('d M Y, h:i A') }}
                                </p>
                                <p class="mt-2 text-sm text-slate-600">{{ $assignment->description ?: 'No extra instructions.' }}</p>
                                <p class="mt-2 text-xs text-slate-500">Max marks: {{ $assignment->max_marks }}</p>
                                @if ($assignment->attachment_path)
                                    <a href="{{ route('student.assignments.download', $assignment) }}" class="mt-2 inline-flex text-sm font-medium text-slate-700 underline">
                                        Download assignment attachment
                                    </a>
                                @endif
                            </div>

                            <div class="module-subcard !rounded-2xl !px-4 !py-3 text-sm">
                                @if ($submission)
                                    <p class="font-semibold uppercase tracking-wide text-slate-700">{{ $submission->status }}</p>
                                    @if ($submission->status === 'graded')
                                        <p class="mt-1 text-slate-600">Marks: {{ number_format($submission->marks_obtained, 2) }} / {{ $assignment->max_marks }}</p>
                                    @else
                                        <p class="mt-1 text-slate-600">Submitted and waiting for review.</p>
                                    @endif
                                @else
                                    <p class="font-semibold uppercase tracking-wide text-slate-700">Not submitted</p>
                                    <p class="mt-1 text-slate-600">Submit before the due date if required by your teacher.</p>
                                @endif
                            </div>
                        </div>

                        @if ($submission)
                            <div class="module-subcard mt-5">
                                <h4 class="font-semibold text-slate-900">Your Submission</h4>
                                @if ($submission->answer_text)
                                    <p class="mt-2 text-sm text-slate-600">{{ $submission->answer_text }}</p>
                                @endif
                                @if ($submission->file_path)
                                    <a href="{{ route('student.assignment-submissions.download', $submission) }}" class="mt-2 inline-flex text-sm font-medium text-slate-700 underline">
                                        Download your submitted file
                                    </a>
                                @endif
                                @if ($submission->teacher_feedback)
                                    <p class="mt-3 text-sm text-slate-600"><span class="font-semibold">Feedback:</span> {{ $submission->teacher_feedback }}</p>
                                @endif
                            </div>
                        @endif

                        <form method="POST" action="{{ route('student.assignments.submit', $assignment) }}" enctype="multipart/form-data" class="mt-5 grid gap-4 lg:grid-cols-2">
                            @csrf
                            <div class="lg:col-span-2">
                                <label for="answer_text_{{ $assignment->id }}" class="block text-sm font-medium text-gray-700">Answer Text</label>
                                <textarea id="answer_text_{{ $assignment->id }}" name="answer_text" rows="4" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('answer_text', $submission?->answer_text) }}</textarea>
                            </div>

                            <div>
                                <label for="submission_file_{{ $assignment->id }}" class="block text-sm font-medium text-gray-700">Submission File</label>
                                <input id="submission_file_{{ $assignment->id }}" name="submission_file" type="file" accept=".pdf,.doc,.docx,.zip,.jpg,.jpeg,.png" class="mt-1 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-1 text-xs text-gray-500">Allowed: PDF, DOC, DOCX, ZIP, JPG, PNG. Max 20 MB.</p>
                            </div>

                            <div class="flex items-end">
                                <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                                    {{ $submission ? 'Resubmit Assignment' : 'Submit Assignment' }}
                                </button>
                            </div>
                        </form>
                    </div>
                @empty
                    <div class="module-card text-sm text-slate-500">
                        No assignments are available for your enrolled classes yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
