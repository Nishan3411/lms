@php
    $user = auth()->user();
    $role = $user?->role;
    $linkCollection = collect();

    $homeRoute = match ($role) {
        'admin' => route('admin.dashboard'),
        'teacher' => route('teacher.dashboard'),
        'student' => route('student.dashboard'),
        'parent' => route('parent.dashboard'),
        default => url('/'),
    };

    $links = match ($role) {
        'admin' => [
            ['label' => 'Dashboard', 'route' => route('admin.dashboard'), 'active' => 'admin.dashboard'],
            ['label' => 'Users', 'route' => route('admin.users'), 'active' => 'admin.users*'],
            ['label' => 'Curriculum', 'route' => route('admin.curriculum'), 'active' => 'admin.curriculum'],
            ['label' => 'Enrollment', 'route' => route('admin.enrollment'), 'active' => 'admin.enrollment'],
            ['label' => 'Teachers', 'route' => route('admin.assign-teacher'), 'active' => 'admin.assign-teacher*'],
            ['label' => 'Fees', 'route' => route('admin.fees.index'), 'active' => 'admin.fees*'],
            ['label' => 'Attendance', 'route' => route('admin.attendance.index'), 'active' => 'admin.attendance*'],
            ['label' => 'Results', 'route' => route('admin.results.index'), 'active' => 'admin.results*'],
            ['label' => 'Timetable', 'route' => route('admin.timetable.index'), 'active' => 'admin.timetable*'],
            ['label' => 'Announcements', 'route' => route('admin.announcements.index'), 'active' => 'admin.announcements*'],
            ['label' => 'Leaves', 'route' => route('admin.leave-requests.index'), 'active' => 'admin.leave-requests*'],
        ],
        'teacher' => [
            ['label' => 'Dashboard', 'route' => route('teacher.dashboard'), 'active' => 'teacher.dashboard'],
            ['label' => 'Materials', 'route' => route('teacher.materials'), 'active' => 'teacher.materials*'],
            ['label' => 'Assignments', 'route' => route('teacher.assignments.index'), 'active' => 'teacher.assignments*'],
            ['label' => 'Reports', 'route' => route('teacher.student-reports.index'), 'active' => 'teacher.student-reports*'],
            ['label' => 'Exams', 'route' => route('teacher.exams.index'), 'active' => 'teacher.exams*'],
            ['label' => 'Timetable', 'route' => route('teacher.timetable.index'), 'active' => 'teacher.timetable*'],
            ['label' => 'Announcements', 'route' => route('teacher.announcements.index'), 'active' => 'teacher.announcements*'],
            ['label' => 'Leaves', 'route' => route('teacher.leave-requests.index'), 'active' => 'teacher.leave-requests*'],
            ['label' => 'Attendance', 'route' => route('teacher.attendance.index'), 'active' => 'teacher.attendance*'],
        ],
        'student' => [
            ['label' => 'Dashboard', 'route' => route('student.dashboard'), 'active' => 'student.dashboard'],
            ['label' => 'Report', 'route' => route('student.report.show'), 'active' => 'student.report*'],
            ['label' => 'Materials', 'route' => route('student.materials'), 'active' => 'student.materials*'],
            ['label' => 'Assignments', 'route' => route('student.assignments.index'), 'active' => 'student.assignments*'],
            ['label' => 'Fees', 'route' => route('student.fees.index'), 'active' => 'student.fees*'],
            ['label' => 'Results', 'route' => route('student.results.index'), 'active' => 'student.results*'],
            ['label' => 'Timetable', 'route' => route('student.timetable.index'), 'active' => 'student.timetable*'],
            ['label' => 'Announcements', 'route' => route('student.announcements.index'), 'active' => 'student.announcements*'],
            ['label' => 'Leaves', 'route' => route('student.leave-requests.index'), 'active' => 'student.leave-requests*'],
            ['label' => 'Attendance', 'route' => route('student.attendance.index'), 'active' => 'student.attendance*'],
        ],
        'parent' => [
            ['label' => 'Dashboard', 'route' => route('parent.dashboard'), 'active' => 'parent.dashboard'],
            ['label' => 'Reports', 'route' => route('parent.student-reports.index'), 'active' => 'parent.student-reports*'],
            ['label' => 'Fees', 'route' => route('parent.fees.index'), 'active' => 'parent.fees*'],
            ['label' => 'Results', 'route' => route('parent.results.index'), 'active' => 'parent.results*'],
            ['label' => 'Timetable', 'route' => route('parent.timetable.index'), 'active' => 'parent.timetable*'],
            ['label' => 'Announcements', 'route' => route('parent.announcements.index'), 'active' => 'parent.announcements*'],
            ['label' => 'Leaves', 'route' => route('parent.leave-requests.index'), 'active' => 'parent.leave-requests*'],
            ['label' => 'Attendance', 'route' => route('parent.attendance.index'), 'active' => 'parent.attendance*'],
        ],
        default => [],
    };

    $linkCollection = collect($links);
    $activeLink = $linkCollection->first(fn ($link) => request()->routeIs($link['active']));
    $primaryLinks = $linkCollection->take(5)->values();

    if ($activeLink && $primaryLinks->doesntContain(fn ($link) => $link['route'] === $activeLink['route'])) {
        $primaryLinks = $primaryLinks
            ->slice(0, max(0, $primaryLinks->count() - 1))
            ->push($activeLink)
            ->unique('route')
            ->values();
    }

    $secondaryLinks = $linkCollection
        ->reject(fn ($link) => $primaryLinks->contains(fn ($primary) => $primary['route'] === $link['route']))
        ->values();
@endphp

<nav class="shell-header sticky top-0 z-40">
    <div class="content-wrap py-4">
        <div class="surface-card !rounded-[30px] !p-3 sm:!p-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ $homeRoute }}" class="flex items-center">
                        <x-brand-logo class="h-12 w-auto sm:h-14" alt="LMS Portal" />
                    </a>

                    @if ($role)
                        <span class="soft-pill hidden sm:inline-flex">{{ ucfirst($role) }}</span>
                    @endif
                </div>

                <div class="flex items-center justify-between gap-3 sm:justify-end">
                    @if ($role)
                        <span class="soft-pill sm:hidden">{{ ucfirst($role) }}</span>
                    @endif

                    @auth
                        <x-dropdown align="right" width="48" contentClasses="py-2 bg-white/95 backdrop-blur-xl">
                            <x-slot name="trigger">
                                <button class="topbar-button">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-xs font-semibold uppercase text-white">
                                        {{ $user->initials() }}
                                    </span>
                                    <span class="hidden text-left sm:block">
                                        <span class="block text-sm font-semibold text-slate-900">{{ $user->name }}</span>
                                        <span class="block text-xs text-slate-500">Open menu</span>
                                    </span>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    Profile
                                </x-dropdown-link>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                        Log Out
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    @endauth
                </div>
            </div>

            @if (! empty($links))
                <div class="nav-strip mt-4">
                    <div class="nav-desktop-shell">
                        <div class="nav-links-scroll">
                            <div class="nav-links-row">
                                @foreach ($primaryLinks as $link)
                                    <a
                                        href="{{ $link['route'] }}"
                                        class="{{ request()->routeIs($link['active']) ? 'app-chip app-chip-active' : 'app-chip' }}"
                                    >
                                        {{ $link['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        @if ($secondaryLinks->isNotEmpty())
                            <div class="nav-more-wrap hidden md:block">
                                <x-dropdown align="right" width="w-[21rem]" contentClasses="p-3 bg-white/95 backdrop-blur-xl">
                                    <x-slot name="trigger">
                                        <button type="button" class="{{ $activeLink && $secondaryLinks->contains(fn ($link) => $link['route'] === $activeLink['route']) ? 'app-chip app-chip-active' : 'app-chip' }}">
                                            More
                                            <svg class="ms-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.51a.75.75 0 0 1-1.08 0l-4.25-4.51a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <div class="nav-more-grid">
                                            @foreach ($secondaryLinks as $link)
                                                <a
                                                    href="{{ $link['route'] }}"
                                                    class="{{ request()->routeIs($link['active']) ? 'nav-more-link nav-more-link-active' : 'nav-more-link' }}"
                                                >
                                                    <span class="nav-more-label">{{ $link['label'] }}</span>
                                                    <span class="nav-more-meta">
                                                        {{ request()->routeIs($link['active']) ? 'Current section' : 'Open section' }}
                                                    </span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </x-slot>
                                </x-dropdown>
                            </div>
                        @endif
                    </div>

                    <div class="nav-links-mobile">
                        @foreach ($links as $link)
                            <a
                                href="{{ $link['route'] }}"
                                class="{{ request()->routeIs($link['active']) ? 'app-chip app-chip-active' : 'app-chip' }}"
                            >
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</nav>
