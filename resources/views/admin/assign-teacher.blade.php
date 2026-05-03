<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">Teacher Assignments</h2>
                <p class="module-copy">Assign teachers to classes and remove assignments when needed.</p>
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

            <div class="module-card">
                <h3 class="text-lg font-semibold text-slate-900">Assign Teacher to Class</h3>
                <p class="text-sm text-slate-500">Only teacher accounts can be assigned to classes.</p>

                <form method="POST" action="{{ route('admin.assign-teacher.store') }}" class="mt-5 grid gap-4 md:grid-cols-[1fr_1fr_auto]">
                    @csrf

                    <div>
                        <label for="teacher_id" class="block text-sm font-medium text-gray-700">Teacher</label>
                        <select id="teacher_id" name="teacher_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Select teacher</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" @selected((string) old('teacher_id') === (string) $teacher->id)>{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="class_id" class="block text-sm font-medium text-gray-700">Class</label>
                        <select id="class_id" name="class_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Select class</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @selected((string) old('class_id') === (string) $class->id)>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Assign</button>
                    </div>
                </form>
            </div>

            <div class="module-card">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h3 class="section-heading">Current Assignments</h3>
                        <p class="section-copy">Review which teachers are currently assigned to each class.</p>
                    </div>
                </div>

                <div class="mt-5 grid gap-4 lg:grid-cols-2">
                    @forelse ($assignments as $class)
                        <div class="module-subcard !rounded-[24px] !p-5">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h4 class="font-semibold text-slate-900">{{ $class->name }}</h4>
                                    <p class="text-sm text-slate-500">{{ ucfirst($class->type) }} class</p>
                                </div>
                                <span class="data-pill">{{ $class->teachers->count() }} teachers</span>
                            </div>

                            <div class="mt-4 space-y-2">
                                @forelse ($class->teachers as $teacher)
                                    <div class="flex items-center justify-between gap-3 rounded-2xl bg-white/90 px-3 py-2 text-sm text-gray-700 ring-1 ring-slate-200/70">
                                        <span>{{ $teacher->name }}</span>

                                        <form method="POST" action="{{ route('admin.assign-teacher.destroy') }}">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
                                            <input type="hidden" name="class_id" value="{{ $class->id }}">
                                            <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-700">Remove</button>
                                        </form>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">No teachers assigned yet.</p>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No classes available yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
