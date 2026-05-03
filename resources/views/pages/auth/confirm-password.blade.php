<x-layouts::auth>
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Confirm password')"
            :description="__('Before continuing, please confirm your password for this secure action.')"
        />

        <div class="rounded-[24px] border border-white/80 bg-white/70 p-4 text-sm leading-6 text-slate-600 backdrop-blur">
            This extra check protects sensitive account settings and actions.
        </div>

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-6">
            @csrf

            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('Password')"
                viewable
            />

            <flux:button variant="primary" type="submit" class="w-full" data-test="confirm-password-button">
                {{ __('Confirm') }}
            </flux:button>
        </form>
    </div>
</x-layouts::auth>
