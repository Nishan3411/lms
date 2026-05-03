<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">Enrollment Management</h2>
                <p class="module-copy">Enroll students into classes and maintain parent-child relationships from one place.</p>
            </div>

            <a href="{{ route('admin.dashboard') }}" class="secondary-action">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell">
            @if (session('success'))
                <div class="notice-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="notice-error">{{ session('error') }}</div>
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

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="module-card">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="section-heading">Assign Student to Class</h3>
                            <p class="section-copy">Enroll a student safely without creating duplicate class membership.</p>
                        </div>
                        <span class="data-pill">{{ $students->count() }} students</span>
                    </div>

                    <form method="POST" action="{{ route('admin.enroll-student') }}" class="mt-5 space-y-4">
                        @csrf

                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700">Student</label>
                            <select id="user_id" name="user_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select student</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}" @selected((string) old('user_id') === (string) $student->id)>{{ $student->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="class_room_id" class="block text-sm font-medium text-gray-700">Class</label>
                            <select id="class_room_id" name="class_room_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select class</option>
                                @foreach ($classRooms as $classRoom)
                                    <option value="{{ $classRoom->id }}" @selected((string) old('class_room_id') === (string) $classRoom->id)>{{ $classRoom->name }} ({{ ucfirst($classRoom->type) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                            Enroll Student
                        </button>
                    </form>
                </div>

                <div class="module-card">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="section-heading">Assign Parent to Student</h3>
                            <p class="section-copy">Connect parents to children so their portal reflects the right student data.</p>
                        </div>
                        <span class="data-pill">{{ $parents->count() }} parents</span>
                    </div>

                    <form method="POST" action="{{ route('admin.link-parent') }}" class="mt-5 space-y-4">
                        @csrf

                        <div>
                            <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent</label>
                            <select id="parent_id" name="parent_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select parent</option>
                                @foreach ($parents as $parent)
                                    <option value="{{ $parent->id }}" @selected((string) old('parent_id') === (string) $parent->id)>{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="student_id" class="block text-sm font-medium text-gray-700">Student</label>
                            <select id="student_id" name="student_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select student</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}" @selected((string) old('student_id') === (string) $student->id)>{{ $student->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                            Link Parent
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-2">
                <div class="module-card">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="section-heading">Students Per Class</h3>
                            <p class="section-copy">Review current class rosters and remove optional enrollments where needed.</p>
                        </div>
                        <span class="data-pill">{{ $classRooms->count() }} classes</span>
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse ($classRooms as $classRoom)
                            <div class="module-subcard !rounded-[24px] !p-5">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $classRoom->name }}</h4>
                                        <p class="text-sm text-gray-500">{{ ucfirst($classRoom->type) }} class</p>
                                    </div>
                                    <span class="data-pill">{{ $classRoom->students_count }} students</span>
                                </div>

                                <div class="mt-4 space-y-2">
                                    @forelse ($classRoom->students as $student)
                                        <div class="flex items-center justify-between gap-3 rounded-2xl bg-white/90 px-3 py-2 text-sm text-gray-700 ring-1 ring-slate-200/70">
                                            <span>{{ $student->name }}</span>

                                            @if ($classRoom->type === 'optional')
                                                <form method="POST" action="{{ route('admin.enroll-student.destroy') }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="user_id" value="{{ $student->id }}">
                                                    <input type="hidden" name="class_room_id" value="{{ $classRoom->id }}">
                                                    <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-700">Remove</button>
                                                </form>
                                            @else
                                                <span class="text-xs font-medium uppercase tracking-wide text-gray-400">Compulsory</span>
                                            @endif
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500">No students enrolled yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div class="module-subcard text-sm text-slate-500">
                                No classes are available yet.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="module-card">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="section-heading">Parent-Child Mapping</h3>
                            <p class="section-copy">Check every linked parent relationship and unlink entries when needed.</p>
                        </div>
                        <span class="data-pill">{{ $parents->count() }} parents</span>
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse ($parents as $parent)
                            <div class="module-subcard !rounded-[24px] !p-5">
                                <div class="flex items-center justify-between gap-4">
                                    <h4 class="font-semibold text-gray-900">{{ $parent->name }}</h4>
                                    <span class="data-pill">{{ $parent->children->count() }} children</span>
                                </div>

                                <div class="mt-4 space-y-2">
                                    @forelse ($parent->children as $child)
                                        <div class="flex items-center justify-between gap-3 rounded-2xl bg-white/90 px-3 py-2 text-sm text-gray-700 ring-1 ring-slate-200/70">
                                            <span>{{ $child->name }}</span>

                                            <form method="POST" action="{{ route('admin.link-parent.destroy') }}">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="parent_id" value="{{ $parent->id }}">
                                                <input type="hidden" name="student_id" value="{{ $child->id }}">
                                                <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-700">Unlink</button>
                                            </form>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500">No students linked yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div class="module-subcard text-sm text-slate-500">
                                No parents are available yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
