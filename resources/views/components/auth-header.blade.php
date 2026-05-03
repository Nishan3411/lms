@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center">
    <p class="auth-kicker">{{ __('Secure access') }}</p>
    <flux:heading size="xl" class="!mt-3 !font-semibold !text-slate-900">{{ $title }}</flux:heading>
    <flux:subheading class="!mt-3 !text-sm !leading-7 !text-slate-500">{{ $description }}</flux:subheading>
</div>
