{{-- components/breadcrumb.blade.php — NFR-08 header breadcrumb (Metronic pattern) --}}
@props(['items' => []])

<div class="flex [.kt-header_&]:below-lg:hidden items-center gap-1.25 text-xs lg:text-sm font-medium mb-2.5 lg:mb-0 [--kt-reparent-target:#contentContainer] lg:[--kt-reparent-target:#headerContainer] [--kt-reparent-mode:prepend] lg:[--kt-reparent-mode:prepend]" data-kt-reparent="true">
    @foreach ($items as $item)
        @if (!$loop->first)
            <i class="ki-filled ki-right text-muted-foreground text-[10px]"></i>
        @endif
        @if ($loop->last)
            <span class="text-mono font-medium">{{ $item['label'] }}</span>
        @elseif (!empty($item['url']))
            <a href="{{ $item['url'] }}" class="text-secondary-foreground hover:text-primary">{{ $item['label'] }}</a>
        @else
            <span class="text-secondary-foreground">{{ $item['label'] }}</span>
        @endif
    @endforeach
</div>
