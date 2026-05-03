@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'notice-success font-medium']) }}>
        {{ $status }}
    </div>
@endif
