<div class="grid gap-6 lg:grid-cols-[260px_minmax(0,1fr)]">
    <div class="module-card !p-4">
        <flux:navlist aria-label="{{ __('Settings') }}">
            <flux:navlist.item :href="route('profile.edit')" wire:navigate>{{ __('Profile') }}</flux:navlist.item>
            <flux:navlist.item :href="route('user-password.edit')" wire:navigate>{{ __('Password') }}</flux:navlist.item>
            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <flux:navlist.item :href="route('two-factor.show')" wire:navigate>{{ __('Two-Factor Auth') }}</flux:navlist.item>
            @endif
            <flux:navlist.item :href="route('appearance.edit')" wire:navigate>{{ __('Appearance') }}</flux:navlist.item>
        </flux:navlist>
    </div>

    <div class="module-card">
        <div>
            <flux:heading>{{ $heading ?? '' }}</flux:heading>
            <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>
        </div>

        <div class="mt-6 w-full max-w-2xl">
            {{ $slot }}
        </div>
    </div>
</div>
