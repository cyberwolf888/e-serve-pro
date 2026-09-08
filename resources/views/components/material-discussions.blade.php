{{-- FR-GR-14 / FR-SW-07 / NFR-08 / M7.8 --}}
@props(['schoolClass', 'material', 'routePrefix'])

<section {{ $attributes->class(['kt-card shadow-none']) }} aria-label="Diskusi materi {{ $material->title }}">
    <div class="kt-card-header min-h-12 px-4">
        <h3 class="kt-card-title flex items-center gap-2 text-sm">
            <i class="ki-filled ki-message-text text-primary"></i>
            <span>Diskusi Materi</span>
            <span class="kt-badge kt-badge-sm kt-badge-outline">{{ $material->discussions_count }} topik</span>
        </h3>
    </div>
    <div class="kt-card-content grid gap-3 px-4 py-3">
        @forelse($material->discussions as $discussion)
            <a href="{{ route($routePrefix.'.classes.discussions.show', [$schoolClass, $discussion]) }}" class="flex items-center justify-between gap-3 text-sm hover:text-primary">
                <span class="min-w-0 truncate font-medium">{{ $discussion->title }}</span>
                <span class="shrink-0 text-xs text-secondary-foreground">
                    {{ $discussion->author->name }} · {{ $discussion->comments_count }} komentar
                </span>
            </a>
        @empty
            <p class="text-sm text-secondary-foreground">Belum ada topik untuk materi ini.</p>
        @endforelse
    </div>
    <div class="kt-card-footer min-h-12 justify-end gap-2 px-4 py-2">
        @can('create', [App\Models\DiscussionTopic::class, $schoolClass, $material])
            <a href="{{ route($routePrefix.'.classes.materials.discussions.create', [$schoolClass, $material]) }}" class="kt-btn kt-btn-sm kt-btn-primary">
                <i class="ki-filled ki-plus"></i>Mulai Diskusi
            </a>
        @endcan
        <a href="{{ route($routePrefix.'.classes.materials.discussions.index', [$schoolClass, $material]) }}" class="kt-btn kt-btn-sm kt-btn-outline">
            Lihat Semua
        </a>
    </div>
</section>
