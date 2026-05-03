<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="antialiased">
        <div class="auth-shell">
            <div class="content-wrap relative py-6 md:py-8">
                <div class="auth-stage">
                    <div class="auth-stage-panel auth-stage-panel-brand">
                        <div class="auth-brand-card h-full">
                            <div class="auth-orb auth-orb-a"></div>
                            <div class="auth-orb auth-orb-b"></div>
                            <div class="auth-orb auth-orb-c"></div>

                            <a href="{{ route('home') }}" class="relative z-20 inline-flex items-center rounded-full border border-white/20 bg-white/10 px-4 py-3 backdrop-blur" wire:navigate>
                                <x-brand-logo class="h-12 w-auto" alt="{{ config('app.name', 'LMS') }}" />
                            </a>

                            <div class="relative z-20 mt-10 max-w-xl space-y-7">
                                <div>
                                    <p class="eyebrow">Academic workflow, refined</p>
                                    <h1 class="mt-4 text-4xl font-semibold leading-tight xl:text-5xl">One campus system for learning, teaching, reporting, and family visibility.</h1>
                                    <p class="mt-5 max-w-lg text-base leading-8 text-white/80">
                                        Sign in to manage the full student journey through curriculum, attendance, assignments, fees, exams, results, announcements, and schedules.
                                    </p>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="hero-meta">
                                        <p class="text-xs uppercase tracking-[0.2em] text-white/65">Role-based access</p>
                                        <p class="mt-2 font-semibold">Admin, teacher, student, parent</p>
                                    </div>
                                    <div class="hero-meta">
                                        <p class="text-xs uppercase tracking-[0.2em] text-white/65">Connected modules</p>
                                        <p class="mt-2 font-semibold">Fees, attendance, results, timetable</p>
                                    </div>
                                </div>

                                <div class="auth-feature-list">
                                    <div class="auth-feature">
                                        <span class="auth-feature-dot"></span>
                                        <div>
                                            <p class="font-semibold text-white">Track progress in one place</p>
                                            <p class="text-sm text-white/70">No more jumping between disconnected school tools.</p>
                                        </div>
                                    </div>
                                    <div class="auth-feature">
                                        <span class="auth-feature-dot"></span>
                                        <div>
                                            <p class="font-semibold text-white">Built for daily use</p>
                                            <p class="text-sm text-white/70">Fast access to the screens people use every day.</p>
                                        </div>
                                    </div>
                                    <div class="auth-feature">
                                        <span class="auth-feature-dot"></span>
                                        <div>
                                            <p class="font-semibold text-white">Clear parent and student visibility</p>
                                            <p class="text-sm text-white/70">Progress, dues, warnings, and schedules stay easy to follow.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-3">
                                    <div class="auth-mini-stat">
                                        <p class="text-xs uppercase tracking-[0.2em] text-white/60">Daily flow</p>
                                        <p class="mt-2 text-xl font-semibold text-white">Attendance</p>
                                    </div>
                                    <div class="auth-mini-stat">
                                        <p class="text-xs uppercase tracking-[0.2em] text-white/60">Performance</p>
                                        <p class="mt-2 text-xl font-semibold text-white">Assignments</p>
                                    </div>
                                    <div class="auth-mini-stat">
                                        <p class="text-xs uppercase tracking-[0.2em] text-white/60">Financials</p>
                                        <p class="mt-2 text-xl font-semibold text-white">Fees</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="auth-stage-panel auth-stage-panel-form">
                        <div class="auth-panel auth-panel-elevated">
                            <a href="{{ route('home') }}" class="mb-6 flex justify-center lg:hidden" wire:navigate>
                                <x-brand-logo class="h-16 w-auto" alt="{{ config('app.name', 'LMS') }}" />
                            </a>
                            <div class="relative z-10">
                                {{ $slot }}
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
                @endphp

                <div class="mt-4 block lg:hidden">
                    <div class="auth-brand-quote !border-slate-200/70 !bg-white/65 !text-slate-600">
                        <p>&ldquo;{{ trim($message) }}&rdquo;</p>
                        <footer class="mt-4 display-font text-base font-semibold text-slate-900">{{ trim($author) }}</footer>
                    </div>
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
