@props([
    'iconClass' => 'size-10',
    'textClass' => 'text-xl',
    'showText' => true,
])

<span {{ $attributes->class(['inline-flex items-center gap-2']) }}>
    <img class="{{ $iconClass }} object-contain" src="{{ asset('assets/media/logo-pro-bi-smart.png') }}" alt="" aria-hidden="true"/>
    @if ($showText)
        <span class="{{ $textClass }} font-semibold tracking-tight text-mono">{{ config('app.name') }}</span>
    @endif
</span>
