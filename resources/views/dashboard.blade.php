<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">{{ __('Dashboard') }}</h2>
                <p class="module-copy">Your account is active and ready to use.</p>
            </div>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell max-w-4xl">
            <div class="module-card">
                <h3 class="text-lg font-semibold text-slate-900">{{ __("You're logged in!") }}</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    Use the navigation to open your role-specific dashboard and continue working inside the LMS.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
