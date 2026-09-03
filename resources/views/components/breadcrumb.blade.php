{{-- NFR-08 — Metronic Demo 2 toolbar breadcrumb --}}
@props(['items' => []])

<nav class="flex items-center flex-wrap gap-1 text-sm font-normal" aria-label="Breadcrumb">
    @foreach ($items as $item)
        @if (!$loop->first)
            <i class="ki-filled ki-right text-muted-foreground text-[10px]"></i>
        @endif
        @if ($loop->last)
            <span class="text-primary font-semibold">{{ $item['label'] }}</span>
        @elseif (!empty($item['url']))
            <a href="{{ $item['url'] }}" class="text-secondary-foreground hover:text-primary">{{ $item['label'] }}</a>
        @else
            <span class="text-secondary-foreground">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
