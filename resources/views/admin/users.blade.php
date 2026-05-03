<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">
                    User Management
                </h2>
                <p class="module-copy">
                    Create and maintain admin, teacher, student, and parent accounts.
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

            @if (session('error'))
                <div class="notice-error">
                    {{ session('error') }}
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

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="stat-card">
                    <p class="text-sm font-medium text-gray-500">Admins</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['admins'] }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-sm font-medium text-gray-500">Teachers</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['teachers'] }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-sm font-medium text-gray-500">Students</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['students'] }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-sm font-medium text-gray-500">Parents</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['parents'] }}</p>
                </div>
            </div>

            <div class="module-card">
                <h3 class="text-lg font-semibold text-slate-900">Create User</h3>
                <p class="text-sm text-slate-500">Use this instead of public registration when you need to create internal LMS accounts.</p>

                <form method="POST" action="{{ route('admin.users.store') }}" class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                    @csrf

                    <input type="text" name="name" value="{{ old('name') }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Full name" required>
                    <input type="email" name="email" value="{{ old('email') }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Email address" required>
                    <select name="role" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Select role</option>
                        <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                        <option value="teacher" @selected(old('role') === 'teacher')>Teacher</option>
                        <option value="student" @selected(old('role') === 'student')>Student</option>
                        <option value="parent" @selected(old('role') === 'parent')>Parent</option>
                    </select>
                    <input type="password" name="password" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Password" required>
                    <input type="password" name="password_confirmation" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Confirm password" required>

                    <button type="submit" class="md:col-span-2 xl:col-span-5 inline-flex items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                        Create User
                    </button>
                </form>
            </div>

            <div class="space-y-6">
                @foreach (['admin' => 'Admins', 'teacher' => 'Teachers', 'student' => 'Students', 'parent' => 'Parents'] as $role => $label)
                    <div class="module-card">
                        <h3 class="text-lg font-semibold text-slate-900">{{ $label }}</h3>
                        <p class="text-sm text-slate-500">Manage existing {{ strtolower($label) }} accounts.</p>

                        <div class="mt-5 space-y-4">
                            @forelse ($usersByRole->get($role, collect()) as $managedUser)
                                <div class="module-subcard !rounded-[24px] !p-5">
                                    <form method="POST" action="{{ route('admin.users.update', $managedUser) }}" class="grid gap-3 lg:grid-cols-[1fr_1fr_180px_1fr]">
                                        @csrf
                                        @method('PATCH')
                                        <input type="text" name="name" value="{{ $managedUser->name }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        <input type="email" name="email" value="{{ $managedUser->email }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        <select name="role" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                            <option value="admin" @selected($managedUser->role === 'admin')>Admin</option>
                                            <option value="teacher" @selected($managedUser->role === 'teacher')>Teacher</option>
                                            <option value="student" @selected($managedUser->role === 'student')>Student</option>
                                            <option value="parent" @selected($managedUser->role === 'parent')>Parent</option>
                                        </select>
                                        <input type="password" name="password" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="New password (optional)">
                                        <input type="password" name="password_confirmation" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 lg:col-span-2" placeholder="Confirm new password">
                                        <div class="flex items-center gap-3 lg:col-span-2">
                                            <button type="submit" class="rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-100">
                                                Update
                                            </button>
                                            <span class="text-sm text-gray-500">
                                                @if ($managedUser->role === 'teacher')
                                                    {{ $managedUser->teaching_classes_count }} classes assigned
                                                @elseif ($managedUser->role === 'student')
                                                    {{ $managedUser->enrolled_classes_count }} classes enrolled, {{ $managedUser->parents_count }} parents linked
                                                @elseif ($managedUser->role === 'parent')
                                                    {{ $managedUser->children_count }} children linked
                                                @else
                                                    Admin account
                                                @endif
                                            </span>
                                        </div>
                                    </form>

                                    <form method="POST" action="{{ route('admin.users.destroy', $managedUser) }}" class="mt-3">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700">
                                            Delete User
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">No {{ strtolower($label) }} found.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
