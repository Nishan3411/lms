@props([
    'alt' => config('app.name', 'LMS').' logo',
])

<img
    src="{{ asset('images/lms-logo-icon.png') }}"
    alt="{{ $alt }}"
    {{ $attributes->merge(['class' => 'h-12 w-auto object-contain']) }}
>
