@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="{{ config('app.name', 'LMS') }}" {{ $attributes }}>
        <x-slot name="logo" class="flex h-8 items-center">
            <x-brand-logo class="h-8 w-auto" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="{{ config('app.name', 'LMS') }}" {{ $attributes }}>
        <x-slot name="logo" class="flex h-8 items-center">
            <x-brand-logo class="h-8 w-auto" />
        </x-slot>
    </flux:brand>
@endif
