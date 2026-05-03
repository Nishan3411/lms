@php
    $authTab = request('auth');
    $oldAuthTab = old('_auth_modal');
    $activeAuthTab = in_array($oldAuthTab ?? $authTab, ['login', 'register'], true)
        ? ($oldAuthTab ?? $authTab)
        : 'login';
    $showAuthModal = $errors->any() || in_array($authTab, ['login', 'register'], true) || session('status');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'LMS') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,700|space-grotesk:500,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    x-data="{
        open: @js($showAuthModal),
        activeTab: @js($activeAuthTab),
        openAuth(tab) {
            this.activeTab = tab;
            this.open = true;
        },
        closeAuth() {
            this.open = false;
        },
        syncUrl() {
            const url = new URL(window.location.href);

            if (this.open) {
                url.searchParams.set('auth', this.activeTab);
            } else {
                url.searchParams.delete('auth');
            }

            window.history.replaceState({}, '', url);
        },
        init() {
            document.body.classList.toggle('auth-modal-open', this.open);

            this.$watch('open', (value) => {
                document.body.classList.toggle('auth-modal-open', value);
                this.syncUrl();
            });

            this.$watch('activeTab', () => {
                if (this.open) {
                    this.syncUrl();
                }
            });
        },
    }"
    @keydown.escape.window="closeAuth()"
    class="antialiased"
>
    <div class="welcome-shell" :class="{ 'welcome-shell-modal': open }">
        <div class="welcome-page-stage relative z-10 pb-16">
            <div class="content-wrap py-6">
                <div class="flex flex-col gap-4 rounded-[30px] border border-white/70 bg-white/70 px-5 py-4 backdrop-blur-xl sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <x-brand-logo class="h-14 w-auto" alt="LMS Portal" />
                        <div class="hidden items-center gap-2 rounded-full border border-slate-200 bg-white/85 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 md:inline-flex">
                            Campus Operating System
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="#roles" class="secondary-action">Explore roles</a>
                        <a href="#modules" class="secondary-action">View modules</a>
                        @auth
                            <a href="{{ url('/dashboard') }}" class="primary-action">Go to Dashboard</a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="secondary-action">Log Out</button>
                            </form>
                        @else
                            <a
                                href="{{ route('home', ['auth' => 'login']) }}"
                                class="secondary-action"
                                @click.prevent="openAuth('login')"
                            >
                                Login
                            </a>
                            <a
                                href="{{ route('home', ['auth' => 'register']) }}"
                                class="primary-action"
                                @click.prevent="openAuth('register')"
                            >
                                Create Account
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            <div class="content-wrap pb-10 pt-4">
                <section class="welcome-panel">
                    <div class="grid gap-10 xl:grid-cols-[1.18fr_0.82fr] xl:items-start">
                        <div>
                            <p class="eyebrow !text-teal-800/70">Connected learning, calmer operations</p>
                            <h1 class="mt-4 max-w-4xl text-4xl font-bold leading-[0.95] text-slate-900 sm:text-5xl lg:text-6xl xl:text-7xl">
                                Run your campus from one clear LMS.
                            </h1>
                            <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">
                                Teaching, attendance, results, fees, and communication in one place.
                            </p>

                            <div class="welcome-cta-row">
                                @guest
                                    <a
                                        href="{{ route('home', ['auth' => 'login']) }}"
                                        class="primary-action"
                                        @click.prevent="openAuth('login')"
                                    >
                                        Enter the portal
                                    </a>
                                    <a
                                        href="{{ route('home', ['auth' => 'register']) }}"
                                        class="secondary-action"
                                        @click.prevent="openAuth('register')"
                                    >
                                        Student or parent signup
                                    </a>
                                @else
                                    <a href="{{ url('/dashboard') }}" class="primary-action">Open dashboard</a>
                                @endguest
                                <a href="#workflow" class="secondary-action">See how it works</a>
                            </div>

                            <div class="welcome-stat-grid">
                                <div class="welcome-stat-card">
                                    <p class="welcome-stat-label">Core Workflows</p>
                                    <p class="welcome-stat-value">10+</p>
                                    <p class="welcome-stat-copy">Attendance, fees, exams, timetable, and more.</p>
                                </div>
                                <div class="welcome-stat-card">
                                    <p class="welcome-stat-label">User Roles</p>
                                    <p class="welcome-stat-value">4</p>
                                    <p class="welcome-stat-copy">Admin, teacher, student, and parent.</p>
                                </div>
                                <div class="welcome-stat-card">
                                    <p class="welcome-stat-label">One Source</p>
                                    <p class="welcome-stat-value">Unified</p>
                                    <p class="welcome-stat-copy">Records and daily work stay in sync.</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="hero-banner hero-banner-admin">
                                <p class="eyebrow">Admin control</p>
                                <h2 class="mt-3 text-3xl font-semibold">See the full campus at once.</h2>
                                <p class="mt-3 max-w-md text-sm leading-7 text-white/80">
                                    Manage users, classes, fees, reports, and announcements from one dashboard.
                                </p>
                                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                                    <div class="hero-meta">
                                        <p class="text-xs uppercase tracking-[0.2em] text-white/65">Tracking</p>
                                        <p class="mt-2 font-semibold">Attendance, dues, leave, results</p>
                                    </div>
                                    <div class="hero-meta">
                                        <p class="text-xs uppercase tracking-[0.2em] text-white/65">Coverage</p>
                                        <p class="mt-2 font-semibold">Classes, subjects, teachers, students</p>
                                    </div>
                                </div>
                            </div>

                            <div class="welcome-side-grid">
                                <div class="welcome-side-card welcome-side-card-teacher">
                                    <p class="welcome-side-label">Teacher flow</p>
                                    <p class="welcome-side-title">Classes, attendance, materials, assignments</p>
                                    <p class="welcome-side-copy">Built for daily teaching.</p>
                                </div>
                                <div class="welcome-side-card welcome-side-card-parent">
                                    <p class="welcome-side-label">Family visibility</p>
                                    <p class="welcome-side-title">Fees, results, timetable, warnings</p>
                                    <p class="welcome-side-copy">Parents stay updated in one place.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="content-wrap">
                <section id="roles" class="space-y-6 py-8">
                    <div class="max-w-2xl">
                        <p class="eyebrow !text-slate-500">Built for every role</p>
                        <h2 class="mt-3 text-3xl font-semibold text-slate-900 sm:text-4xl">Each user gets the tools they actually need.</h2>
                        <p class="mt-3 text-base leading-7 text-slate-600">
                            One platform, focused views for every role.
                        </p>
                    </div>

                    <div class="grid gap-5 lg:grid-cols-4 sm:grid-cols-2">
                        <div class="action-card">
                            <span class="soft-pill">Admin</span>
                            <h3 class="action-title">Institution-wide control</h3>
                            <p class="action-copy">Manage users, curriculum, fees, reports, and operations.</p>
                        </div>
                        <div class="action-card">
                            <span class="soft-pill">Teacher</span>
                            <h3 class="action-title">Daily teaching workflow</h3>
                            <p class="action-copy">Upload materials, post assignments, mark attendance, and grade work.</p>
                        </div>
                        <div class="action-card">
                            <span class="soft-pill">Student</span>
                            <h3 class="action-title">Clear academic path</h3>
                            <p class="action-copy">View materials, submit work, track attendance, and check results.</p>
                        </div>
                        <div class="action-card">
                            <span class="soft-pill">Parent</span>
                            <h3 class="action-title">Useful family visibility</h3>
                            <p class="action-copy">See progress, attendance, dues, and key updates.</p>
                        </div>
                    </div>
                </section>
            </div>

            <div class="content-wrap">
                <section id="modules" class="space-y-6 py-8">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div class="max-w-2xl">
                            <p class="eyebrow !text-slate-500">Everything in one workflow</p>
                            <h2 class="mt-3 text-3xl font-semibold text-slate-900 sm:text-4xl">Everything you need in one system.</h2>
                        </div>
                        <p class="max-w-xl text-sm leading-7 text-slate-600">
                            Core modules for daily academic work.
                        </p>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <div class="surface-card">
                            <p class="text-sm font-medium text-slate-500">Academic Structure</p>
                            <h3 class="mt-3 text-2xl font-semibold text-slate-900">Curriculum Management</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">Manage classes, subjects, and topics.</p>
                        </div>
                        <div class="surface-card">
                            <p class="text-sm font-medium text-slate-500">Operations</p>
                            <h3 class="mt-3 text-2xl font-semibold text-slate-900">Enrollment and Linking</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">Assign students and link parents.</p>
                        </div>
                        <div class="surface-card">
                            <p class="text-sm font-medium text-slate-500">Teaching</p>
                            <h3 class="mt-3 text-2xl font-semibold text-slate-900">Materials and Assignments</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">Share materials and collect student work.</p>
                        </div>
                        <div class="surface-card">
                            <p class="text-sm font-medium text-slate-500">Finance</p>
                            <h3 class="mt-3 text-2xl font-semibold text-slate-900">Semester Fee Tracking</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">Track paid, partial, and pending fees.</p>
                        </div>
                        <div class="surface-card">
                            <p class="text-sm font-medium text-slate-500">Monitoring</p>
                            <h3 class="mt-3 text-2xl font-semibold text-slate-900">Attendance Intelligence</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">Mark attendance and review trends.</p>
                        </div>
                        <div class="surface-card">
                            <p class="text-sm font-medium text-slate-500">Reporting</p>
                            <h3 class="mt-3 text-2xl font-semibold text-slate-900">Results and Exams</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">Manage exams, marks, and results.</p>
                        </div>
                    </div>
                </section>
            </div>

            <div class="content-wrap">
                <section id="workflow" class="grid gap-6 py-8 xl:grid-cols-[0.95fr_1.05fr]">
                    <div class="surface-card">
                        <p class="eyebrow !text-slate-500">Why it feels better</p>
                        <h2 class="mt-3 text-3xl font-semibold text-slate-900">Powerful, but still simple to use.</h2>
                        <p class="mt-4 text-base leading-7 text-slate-600">
                            The system stays broad, but each screen stays focused.
                        </p>
                        <div class="mt-6 space-y-4">
                            <div class="module-subcard">
                                <p class="text-sm font-semibold text-slate-900">Focused dashboards</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Each role sees what matters first.</p>
                            </div>
                            <div class="module-subcard">
                                <p class="text-sm font-semibold text-slate-900">Connected records</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Attendance, results, fees, and updates stay connected.</p>
                            </div>
                            <div class="module-subcard">
                                <p class="text-sm font-semibold text-slate-900">Scalable admin visibility</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Clear reporting as the platform grows.</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="hero-banner hero-banner-teacher">
                            <p class="eyebrow">Teacher experience</p>
                            <h3 class="mt-3 text-2xl font-semibold">Daily actions stay fast.</h3>
                            <p class="mt-3 text-sm leading-7 text-white/80">Attendance, grading, and materials stay close at hand.</p>
                        </div>
                        <div class="hero-banner hero-banner-parent">
                            <p class="eyebrow">Parent visibility</p>
                            <h3 class="mt-3 text-2xl font-semibold">Family updates stay clear.</h3>
                            <p class="mt-3 text-sm leading-7 text-white/80">Warnings, dues, results, and updates in one view.</p>
                        </div>
                        <div class="hero-banner hero-banner-student md:col-span-2">
                            <p class="eyebrow">Student experience</p>
                            <h3 class="mt-3 text-3xl font-semibold">One place to learn, submit, track, and improve.</h3>
                            <p class="mt-3 max-w-2xl text-sm leading-7 text-white/80">Materials, assignments, attendance, results, and dues in one place.</p>
                        </div>
                    </div>
                </section>
            </div>

            <div class="content-wrap">
                <section class="py-8">
                    <div class="hero-banner overflow-hidden rounded-[38px]">
                        <div class="relative z-10 grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center">
                            <div>
                                <p class="eyebrow">Ready to enter the platform?</p>
                                <h2 class="mt-3 max-w-2xl text-3xl font-semibold sm:text-4xl">Open the portal and get started.</h2>
                                <p class="mt-4 max-w-2xl text-sm leading-7 text-white/80">
                                    Sign in and continue from your role-based dashboard.
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-3 lg:justify-end">
                                @guest
                                    <a
                                        href="{{ route('home', ['auth' => 'login']) }}"
                                        class="primary-action !bg-white !text-slate-900 hover:!bg-slate-100"
                                        @click.prevent="openAuth('login')"
                                    >
                                        Login
                                    </a>
                                    <a
                                        href="{{ route('home', ['auth' => 'register']) }}"
                                        class="secondary-action !border-white/20 !bg-white/10 !text-white hover:!bg-white/15"
                                        @click.prevent="openAuth('register')"
                                    >
                                        Create Account
                                    </a>
                                @else
                                    <a href="{{ url('/dashboard') }}" class="primary-action !bg-white !text-slate-900 hover:!bg-slate-100">Go to Dashboard</a>
                                @endguest
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        @guest
            <div
                x-cloak
                x-show="open"
                x-transition.opacity.duration.250ms
                class="auth-modal-backdrop"
                @click="closeAuth()"
            ></div>

            <div
                x-cloak
                x-show="open"
                x-transition.opacity.duration.250ms
                class="auth-modal-shell"
                aria-modal="true"
                role="dialog"
            >
                <div
                    class="auth-modal-panel"
                    x-show="open"
                    x-transition:enter="transition ease-out duration-250"
                    x-transition:enter-start="opacity-0 translate-y-6 scale-[0.98]"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 scale-[0.98]"
                    @click.stop
                >
                    <button type="button" class="auth-modal-close" @click="closeAuth()" aria-label="Close authentication dialog">
                        <span aria-hidden="true">&times;</span>
                    </button>

                    <div class="auth-modal-layout">
                        <aside class="auth-modal-brand">
                            <div class="auth-orb auth-orb-a"></div>
                            <div class="auth-orb auth-orb-c"></div>

                            <div class="relative z-10">
                                <div class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-3 backdrop-blur">
                                    <x-brand-logo class="h-12 w-auto" alt="{{ config('app.name', 'LMS') }}" />
                                </div>

                                <p class="mt-8 text-xs font-semibold uppercase tracking-[0.26em] text-white/70">Welcome to the portal</p>
                                <h2 class="mt-4 max-w-md text-3xl font-semibold leading-tight text-white sm:text-4xl">
                                    Sign in or create your account without leaving the homepage.
                                </h2>
                                <p class="mt-4 max-w-md text-sm leading-7 text-white/80">
                                    A simple entry point for students, teachers, and parents.
                                </p>
                            </div>
                        </aside>

                        <section class="auth-modal-form">
                            <div class="auth-modal-tabs" role="tablist" aria-label="Authentication tabs">
                                <button
                                    type="button"
                                    class="auth-modal-tab"
                                    :class="{ 'auth-modal-tab-active': activeTab === 'login' }"
                                    @click="activeTab = 'login'"
                                    :aria-selected="activeTab === 'login'"
                                >
                                    Login
                                </button>
                                <button
                                    type="button"
                                    class="auth-modal-tab"
                                    :class="{ 'auth-modal-tab-active': activeTab === 'register' }"
                                    @click="activeTab = 'register'"
                                    :aria-selected="activeTab === 'register'"
                                >
                                    Create account
                                </button>
                            </div>

                            <div x-show="activeTab === 'login'" x-transition.opacity.duration.200ms>
                                <div class="flex flex-col gap-6">
                                    <div>
                                        <p class="auth-kicker">Secure login</p>
                                        <h3 class="mt-3 text-3xl font-semibold text-slate-900">Welcome back</h3>
                                        <p class="mt-3 text-sm leading-7 text-slate-500">
                                            Use your email and password to continue.
                                        </p>
                                    </div>

                                    @if (session('status'))
                                        <div class="notice-success">
                                            {{ session('status') }}
                                        </div>
                                    @endif

                                    @if ($errors->any() && $activeAuthTab === 'login')
                                        <div class="notice-error">
                                            {{ $errors->first() }}
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('login.store') }}" class="auth-form-stack">
                                        @csrf
                                        <input type="hidden" name="_auth_modal" value="login">

                                        <div class="auth-field">
                                            <label for="landing-login-email" class="auth-label">Email address</label>
                                            <input
                                                id="landing-login-email"
                                                name="email"
                                                type="email"
                                                class="auth-input"
                                                value="{{ old('_auth_modal') === 'login' ? old('email') : '' }}"
                                                autocomplete="email"
                                                placeholder="email@example.com"
                                                required
                                            >
                                            @error('email')
                                                @if ($activeAuthTab === 'login')
                                                    <p class="auth-error">{{ $message }}</p>
                                                @endif
                                            @enderror
                                        </div>

                                        <div class="auth-field">
                                            <div class="flex items-center justify-between gap-3">
                                                <label for="landing-login-password" class="auth-label">Password</label>
                                                @if (Route::has('password.request'))
                                                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-teal-700 hover:text-teal-800">
                                                        Forgot password?
                                                    </a>
                                                @endif
                                            </div>
                                            <input
                                                id="landing-login-password"
                                                name="password"
                                                type="password"
                                                class="auth-input"
                                                autocomplete="current-password"
                                                placeholder="Enter your password"
                                                required
                                            >
                                            @error('password')
                                                @if ($activeAuthTab === 'login')
                                                    <p class="auth-error">{{ $message }}</p>
                                                @endif
                                            @enderror
                                        </div>

                                        <label class="auth-soft-panel flex items-center gap-3">
                                            <input
                                                type="checkbox"
                                                name="remember"
                                                value="1"
                                                class="h-4 w-4 rounded border-slate-300 text-teal-700 focus:ring-teal-600"
                                                @checked(old('_auth_modal') === 'login' && old('remember'))
                                            >
                                            <span class="text-sm text-slate-600">Remember me on this device</span>
                                        </label>

                                        <button type="submit" class="primary-action auth-submit-button w-full !py-3 !text-base">
                                            Log in
                                        </button>
                                    </form>

                                    <p class="auth-footer-note">
                                        Need a new account?
                                        <button type="button" class="font-semibold text-teal-700 hover:text-teal-800" @click="activeTab = 'register'">
                                            Create one here
                                        </button>
                                    </p>
                                </div>
                            </div>

                            <div x-show="activeTab === 'register'" x-transition.opacity.duration.200ms>
                                <div class="flex flex-col gap-6">
                                    <div>
                                        <p class="auth-kicker">Guided signup</p>
                                        <h3 class="mt-3 text-3xl font-semibold text-slate-900">Create your LMS account</h3>
                                        <p class="mt-3 text-sm leading-7 text-slate-500">
                                            Register as a student, teacher, or parent.
                                        </p>
                                    </div>

                                    @if ($errors->any() && $activeAuthTab === 'register')
                                        <div class="notice-error">
                                            {{ $errors->first() }}
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('register.store') }}" class="auth-form-stack">
                                        @csrf
                                        <input type="hidden" name="_auth_modal" value="register">

                                        <div class="auth-field">
                                            <label for="landing-register-name" class="auth-label">Full name</label>
                                            <input
                                                id="landing-register-name"
                                                name="name"
                                                type="text"
                                                class="auth-input"
                                                value="{{ old('_auth_modal') === 'register' ? old('name') : '' }}"
                                                autocomplete="name"
                                                placeholder="Your full name"
                                                required
                                            >
                                            @error('name')
                                                @if ($activeAuthTab === 'register')
                                                    <p class="auth-error">{{ $message }}</p>
                                                @endif
                                            @enderror
                                        </div>

                                        <div class="auth-field">
                                            <label for="landing-register-email" class="auth-label">Email address</label>
                                            <input
                                                id="landing-register-email"
                                                name="email"
                                                type="email"
                                                class="auth-input"
                                                value="{{ old('_auth_modal') === 'register' ? old('email') : '' }}"
                                                autocomplete="email"
                                                placeholder="email@example.com"
                                                required
                                            >
                                            @error('email')
                                                @if ($activeAuthTab === 'register')
                                                    <p class="auth-error">{{ $message }}</p>
                                                @endif
                                            @enderror
                                        </div>

                                        <div class="auth-field">
                                            <label for="landing-register-role" class="auth-label">Join as</label>
                                            <select
                                                id="landing-register-role"
                                                name="role"
                                                class="auth-input auth-select"
                                                required
                                            >
                                                <option value="">Select your role</option>
                                                <option value="student" @selected(old('_auth_modal') === 'register' && old('role') === 'student')>Student</option>
                                                <option value="teacher" @selected(old('_auth_modal') === 'register' && old('role') === 'teacher')>Teacher</option>
                                                <option value="parent" @selected(old('_auth_modal') === 'register' && old('role') === 'parent')>Parent</option>
                                            </select>
                                            @error('role')
                                                @if ($activeAuthTab === 'register')
                                                    <p class="auth-error">{{ $message }}</p>
                                                @endif
                                            @enderror
                                        </div>

                                        <div class="grid gap-5 md:grid-cols-2">
                                            <div class="auth-field">
                                                <label for="landing-register-password" class="auth-label">Password</label>
                                                <input
                                                    id="landing-register-password"
                                                    name="password"
                                                    type="password"
                                                    class="auth-input"
                                                    autocomplete="new-password"
                                                    placeholder="Create a password"
                                                    required
                                                >
                                                @error('password')
                                                    @if ($activeAuthTab === 'register')
                                                        <p class="auth-error">{{ $message }}</p>
                                                    @endif
                                                @enderror
                                            </div>

                                            <div class="auth-field">
                                                <label for="landing-register-password-confirmation" class="auth-label">Confirm password</label>
                                                <input
                                                    id="landing-register-password-confirmation"
                                                    name="password_confirmation"
                                                    type="password"
                                                    class="auth-input"
                                                    autocomplete="new-password"
                                                    placeholder="Confirm your password"
                                                    required
                                                >
                                                @error('password_confirmation')
                                                    @if ($activeAuthTab === 'register')
                                                        <p class="auth-error">{{ $message }}</p>
                                                    @endif
                                                @enderror
                                            </div>
                                        </div>

                                        <button type="submit" class="primary-action auth-submit-button w-full !py-3 !text-base">
                                            Create account
                                        </button>
                                    </form>

                                    <p class="auth-footer-note">
                                        Already registered?
                                        <button type="button" class="font-semibold text-teal-700 hover:text-teal-800" @click="activeTab = 'login'">
                                            Log in instead
                                        </button>
                                    </p>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        @endguest
    </div>
</body>
</html>
