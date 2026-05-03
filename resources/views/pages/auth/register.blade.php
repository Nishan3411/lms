<x-layouts::auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Set up your learner or family portal with the details below.')" />

        <div class="auth-info-card">
            <div class="flex flex-wrap gap-2">
                <span class="auth-badge">Student access</span>
                <span class="auth-badge">Parent access</span>
                <span class="auth-badge">Secure onboarding</span>
            </div>
            <p class="mt-3 text-sm leading-6 text-slate-600">
                Registration is available for standard LMS users. Admin accounts remain protected and are created separately for security.
            </p>
        </div>

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="auth-form-stack">
            @csrf

            <flux:input
                name="name"
                :label="__('Name')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
            />

            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            <div class="auth-field">
                <label for="register-role" class="auth-label">{{ __('Join as') }}</label>
                <select id="register-role" name="role" class="auth-input auth-select" required>
                    <option value="">{{ __('Select your role') }}</option>
                    <option value="student" @selected(old('role') === 'student')>{{ __('Student') }}</option>
                    <option value="teacher" @selected(old('role') === 'teacher')>{{ __('Teacher') }}</option>
                    <option value="parent" @selected(old('role') === 'parent')>{{ __('Parent') }}</option>
                </select>
                @error('role')
                    <p class="auth-error">{{ $message }}</p>
                @enderror
            </div>

            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                viewable
            />

            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="auth-submit-button w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="auth-footer-note">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('home', ['auth' => 'login'])" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
