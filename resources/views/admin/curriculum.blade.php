<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">
                    Curriculum Management
                </h2>
                <p class="module-copy">
                    Create and organize classes, subjects, and topics for the LMS.
                </p>
            </div>

            <a href="{{ route('admin.dashboard') }}" class="secondary-action">
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell">
            @if (session('success'))
                <div class="notice-success">
                    {{ session('success') }}
                </div>
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

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="module-card">
                    <h3 class="text-lg font-semibold text-gray-900">Add Class</h3>
                    <p class="mt-1 text-sm text-gray-500">Create a compulsory or optional class.</p>

                    <form method="POST" action="{{ route('admin.class-rooms.store') }}" class="mt-4 space-y-4">
                        @csrf
                        <div>
                            <label for="class_room_name" class="block text-sm font-medium text-gray-700">Class Name</label>
                            <input id="class_room_name" name="name" type="text" value="{{ old('name') }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Mathematics" required>
                        </div>

                        <div>
                            <label for="class_room_type" class="block text-sm font-medium text-gray-700">Type</label>
                            <select id="class_room_type" name="type" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="compulsory" @selected(old('type') === 'compulsory')>Compulsory</option>
                                <option value="optional" @selected(old('type') === 'optional')>Optional</option>
                            </select>
                        </div>

                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                            Save Class
                        </button>
                    </form>
                </div>

                <div class="module-card">
                    <h3 class="text-lg font-semibold text-gray-900">Add Subject</h3>
                    <p class="mt-1 text-sm text-gray-500">Attach a subject to an existing class.</p>

                    <form method="POST" action="{{ route('admin.subjects.store') }}" class="mt-4 space-y-4">
                        @csrf
                        <div>
                            <label for="subject_class_room_id" class="block text-sm font-medium text-gray-700">Class</label>
                            <select id="subject_class_room_id" name="class_room_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select class</option>
                                @foreach ($classRooms as $classRoom)
                                    <option value="{{ $classRoom->id }}" @selected((string) old('class_room_id') === (string) $classRoom->id)>{{ $classRoom->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="subject_name" class="block text-sm font-medium text-gray-700">Subject Name</label>
                            <input id="subject_name" name="name" type="text" value="{{ old('name') }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Algebra" required>
                        </div>

                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                            Save Subject
                        </button>
                    </form>
                </div>

                <div class="module-card">
                    <h3 class="text-lg font-semibold text-gray-900">Add Topic</h3>
                    <p class="mt-1 text-sm text-gray-500">Break a subject into teachable topics.</p>

                    <form method="POST" action="{{ route('admin.topics.store') }}" class="mt-4 space-y-4">
                        @csrf
                        <div>
                            <label for="topic_subject_id" class="block text-sm font-medium text-gray-700">Subject</label>
                            <select id="topic_subject_id" name="subject_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select subject</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}" @selected((string) old('subject_id') === (string) $subject->id)>{{ $subject->name }} ({{ $subject->classRoom->name }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="topic_title" class="block text-sm font-medium text-gray-700">Topic Title</label>
                            <input id="topic_title" name="title" type="text" value="{{ old('title') }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Linear Equations" required>
                        </div>

                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                            Save Topic
                        </button>
                    </form>
                </div>
            </div>

            <div class="module-card">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Curriculum Overview</h3>
                        <p class="text-sm text-gray-500">Review and maintain your complete academic structure.</p>
                    </div>
                    <span class="inline-flex w-fit rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-700">
                        {{ $classRooms->count() }} classes
                    </span>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($classRooms as $classRoom)
                        <div class="module-subcard !rounded-[26px] !p-5">
                            <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="text-lg font-semibold text-gray-900">{{ $classRoom->name }}</h4>
                                        <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold uppercase tracking-wide text-gray-600 ring-1 ring-gray-200">
                                            {{ $classRoom->type }}
                                        </span>
                                    </div>
                                    <div class="mt-2 flex flex-wrap gap-4 text-sm text-gray-500">
                                        <span>{{ $classRoom->subjects_count }} subjects</span>
                                        <span>{{ $classRoom->students_count }} students</span>
                                        <span>{{ $classRoom->teachers_count }} teachers</span>
                                    </div>
                                </div>

                                <div class="w-full xl:w-[360px] space-y-2">
                                    <form method="POST" action="{{ route('admin.class-rooms.update', $classRoom) }}" class="grid gap-3 sm:grid-cols-[1fr_auto_auto]">
                                        @csrf
                                        @method('PATCH')
                                        <input name="name" type="text" value="{{ $classRoom->name }}" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        <select name="type" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                            <option value="compulsory" @selected($classRoom->type === 'compulsory')>Compulsory</option>
                                            <option value="optional" @selected($classRoom->type === 'optional')>Optional</option>
                                        </select>
                                        <button type="submit" class="rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-100">
                                            Update
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.class-rooms.destroy', $classRoom) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700">
                                            Delete class
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="mt-5 space-y-3">
                                @forelse ($classRoom->subjects->sortBy('name') as $subject)
                                    <div class="rounded-2xl bg-white/90 p-4 ring-1 ring-slate-200/70">
                                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold uppercase tracking-wide text-gray-400">Subject</p>
                                                <p class="mt-1 text-base font-semibold text-gray-900">{{ $subject->name }}</p>

                                                <div class="mt-3 flex flex-wrap gap-2">
                                                    @forelse ($subject->topics->sortBy('title') as $topic)
                                                    <div class="rounded-2xl bg-slate-50/90 px-3 py-2 ring-1 ring-slate-200/70">
                                                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                                                <form method="POST" action="{{ route('admin.topics.update', $topic) }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                                                                    <input name="title" type="text" value="{{ $topic->title }}" class="min-w-[180px] rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                                    <button type="submit" class="rounded-md bg-white px-3 py-2 text-xs font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-100">
                                                                        Save
                                                                    </button>
                                                                </form>

                                                                <form method="POST" action="{{ route('admin.topics.destroy', $topic) }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-700">
                                                                        Delete
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <span class="text-sm text-gray-500">No topics yet.</span>
                                                    @endforelse
                                                </div>
                                            </div>

                                            <div class="w-full space-y-3 lg:w-[340px]">
                                                <form method="POST" action="{{ route('admin.subjects.update', $subject) }}" class="space-y-3">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="class_room_id" value="{{ $classRoom->id }}">
                                                    <input name="name" type="text" value="{{ $subject->name }}" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                    <button type="submit" class="rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-100">
                                                        Update Subject
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700">
                                                        Delete subject
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('admin.topics.store') }}" class="rounded-2xl border border-dashed border-slate-300 p-3">
                                                    @csrf
                                                    <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                                                    <label class="block text-sm font-medium text-gray-700">Add Topic to {{ $subject->name }}</label>
                                                    <div class="mt-2 flex flex-col gap-2 sm:flex-row">
                                                        <input name="title" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="New topic title" required>
                                                        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                                                            Add
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">No subjects added for this class yet.</p>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[28px] border border-dashed border-slate-300 p-8 text-center">
                            <h4 class="text-lg font-semibold text-gray-900">No classes created yet</h4>
                            <p class="mt-2 text-sm text-gray-500">Start by adding your first class from the form above.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
