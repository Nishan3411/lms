<x-layouts::auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Log in to your account')" :description="__('Use your school email and password to continue into the LMS portal.')" />

        <div class="auth-info-card">
            <div class="flex flex-wrap gap-2">
                <span class="auth-badge">Assignments</span>
                <span class="auth-badge">Attendance</span>
                <span class="auth-badge">Results</span>
                <span class="auth-badge">Fees</span>
            </div>
            <p class="mt-3 text-sm leading-6 text-slate-600">
                Access assignments, attendance, fees, materials, dashboards, and progress data from one secure account.
            </p>
        </div>

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="auth-form-stack">
            @csrf

            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute end-0 top-0 text-sm text-slate-500" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            <div class="auth-soft-panel">
                <flux:checkbox name="remember" :label="__('Remember me on this device')" :checked="old('remember')" />
            </div>

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="auth-submit-button w-full" data-test="login-button">
                    {{ __('Log in') }}
                </flux:button>
            </div>
        </form>

            @if (Route::has('register'))
                <div class="auth-footer-note">
                    <span>{{ __('Don\'t have an account?') }}</span>
                    <flux:link :href="route('home', ['auth' => 'register'])" wire:navigate>{{ __('Sign up') }}</flux:link>
                </div>
            @endif
        </div>
    </x-layouts::auth>
