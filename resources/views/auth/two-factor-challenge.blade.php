<x-guest-layout>
    <form method="POST" action="{{ route('two-factor.login.store') }}">
        @csrf

        <div>
            <x-input-label for="code" :value="__('Authentication Code')" />
            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" autofocus autocomplete="one-time-code" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="recovery_code" :value="__('Recovery Code')" />
            <x-text-input id="recovery_code" class="block mt-1 w-full" type="text" name="recovery_code" autocomplete="one-time-code" />
            <x-input-error :messages="$errors->get('recovery_code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Continue') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
