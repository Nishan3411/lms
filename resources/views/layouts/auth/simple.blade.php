<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="antialiased">
        <div class="auth-shell flex min-h-svh items-center justify-center p-6 md:p-10">
            <div class="w-full max-w-md">
                <div class="auth-panel">
                    <a href="{{ route('home') }}" class="relative z-10 mb-6 flex flex-col items-center gap-3 font-medium" wire:navigate>
                        <x-brand-logo class="h-16 w-auto" alt="{{ config('app.name', 'LMS') }}" />
                    </a>
                    <div class="relative z-10 flex flex-col gap-6">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
