<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">
                    Learning Materials
                </h2>
                <p class="module-copy">
                    Upload PDF, PPT, and PPTX files for classes assigned to you.
                </p>
            </div>

            <a href="{{ route('teacher.dashboard') }}" class="secondary-action">
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

            <div class="module-card">
                <h3 class="text-lg font-semibold text-slate-900">Upload Material</h3>
                <p class="text-sm text-slate-500">Students enrolled in the selected class will be able to download this file.</p>

                <form method="POST" action="{{ route('teacher.materials.store') }}" enctype="multipart/form-data" class="mt-5 grid gap-4 lg:grid-cols-2">
                    @csrf

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <input id="title" name="title" type="text" value="{{ old('title') }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Chapter 1 Presentation" required>
                    </div>

                    <div>
                        <label for="class_room_id" class="block text-sm font-medium text-gray-700">Class</label>
                        <select id="class_room_id" name="class_room_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Select class</option>
                            @foreach ($classRooms as $classRoom)
                                <option value="{{ $classRoom->id }}" @selected((string) old('class_room_id') === (string) $classRoom->id)>
                                    {{ $classRoom->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject</label>
                        <select id="subject_id" name="subject_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">General class material</option>
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
                        <label for="file" class="block text-sm font-medium text-gray-700">File</label>
                        <input id="file" name="file" type="file" accept=".pdf,.ppt,.pptx" class="mt-1 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <p class="mt-1 text-xs text-gray-500">Allowed: PDF, PPT, PPTX. Max size: 20 MB.</p>
                    </div>

                    <div class="lg:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="3" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Short note for students">{{ old('description') }}</textarea>
                    </div>

                    <div class="lg:col-span-2">
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                            Upload Material
                        </button>
                    </div>
                </form>
            </div>

            <div class="module-card">
                <h3 class="text-lg font-semibold text-slate-900">Uploaded Materials</h3>
                <p class="text-sm text-slate-500">Manage files you have uploaded for your classes.</p>

                <div class="mt-5 space-y-4">
                    @forelse ($materials as $material)
                        <div class="module-subcard !rounded-[24px] !p-5">
                            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <h4 class="font-semibold text-slate-900">{{ $material->title }}</h4>
                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $material->classRoom->name }}
                                        @if ($material->subject)
                                            · {{ $material->subject->name }}
                                        @endif
                                    </p>
                                    <p class="mt-2 text-sm text-slate-600">{{ $material->description ?: 'No description provided.' }}</p>
                                    <p class="mt-2 text-xs text-slate-500">
                                        {{ $material->original_filename }} · {{ number_format($material->file_size / 1024, 1) }} KB
                                    </p>
                                </div>

                                <form method="POST" action="{{ route('teacher.materials.destroy', $material) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">You have not uploaded any learning materials yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
