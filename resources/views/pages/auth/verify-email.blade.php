<x-layouts::auth>
    <div class="mt-4 flex flex-col gap-6">
        <x-auth-header :title="__('Verify your email')" :description="__('Finish activating your account by confirming your email address.')" />

        <div class="rounded-[24px] border border-white/80 bg-white/70 p-4 text-center text-sm leading-6 text-slate-600 backdrop-blur">
            {{ __('Please verify your email address by clicking on the link we just emailed to you.') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="rounded-[22px] border border-emerald-200 bg-emerald-50/90 px-4 py-3 text-center text-sm font-medium text-emerald-700">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div class="flex flex-col items-center justify-between gap-3">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                @csrf
                <flux:button type="submit" variant="primary" class="w-full">
                    {{ __('Resend verification email') }}
                </flux:button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button variant="ghost" type="submit" class="cursor-pointer text-sm" data-test="logout-button">
                    {{ __('Log out') }}
                </flux:button>
            </form>
        </div>
    </div>
</x-layouts::auth>
