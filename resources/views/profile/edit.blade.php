<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">{{ __('Profile') }}</h2>
                <p class="module-copy">Manage your account details, password, and account lifecycle settings.</p>
            </div>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell max-w-5xl">
            <div class="space-y-6">
                <div class="module-card">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="module-card">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="module-card">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
