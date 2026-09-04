@props([
    'iconClass' => 'size-10',
    'textClass' => 'text-xl',
    'showText' => true,
])

<span {{ $attributes->class(['inline-flex items-center gap-2']) }}>
    @if ($showText)
        <img class="{{ $iconClass }} w-auto object-contain dark:hidden" src="{{ asset('assets/media/e-serve-pro-black.png') }}" alt="" aria-hidden="true"/>
        <img class="{{ $iconClass }} hidden w-auto object-contain dark:block" src="{{ asset('assets/media/e-serve-pro-white.png') }}" alt="" aria-hidden="true"/>
    @else
        <img class="{{ $iconClass }} object-contain" src="{{ asset('assets/media/logo-e-serve-pro.png') }}" alt="" aria-hidden="true"/>
    @endif
</span>
