{{-- discussions/index.blade.php — FR-SA-07 / FR-GR-14 / FR-SW-07 / NFR-08 / M7.8 --}}
@extends('layouts.app')
@php($indexLabel = $routePrefix === 'admin' ? 'Kelas' : 'Kelas Saya')
@section('breadcrumb')<x-breadcrumb :items="[['label' => $indexLabel, 'url' => route($routePrefix.'.classes.index')], ['label' => $class->name, 'url' => route($routePrefix.'.classes.show', $class)], ['label' => 'Diskusi']]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5">
    @if($routePrefix !== 'siswa')
        @include('guru.classes._tabs', ['class' => $class, 'routePrefix' => $routePrefix])
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-mono">Diskusi Kelas</h1>
            <p class="mt-1 text-sm text-secondary-foreground">{{ $class->name }}</p>
        </div>
        @can('create', [App\Models\DiscussionTopic::class, $class])
            <a href="{{ route($routePrefix.'.classes.discussions.create', $class) }}" class="kt-btn kt-btn-primary">
                <i class="ki-filled ki-plus"></i>Buat Topik
            </a>
        @endcan
    </div>

    @if(! $class->is_active)
        <div class="kt-alert kt-alert-warning">Kelas nonaktif. Diskusi hanya dapat dibaca.</div>
    @endif
    @if(session('success'))<div class="kt-alert kt-alert-success">{{ session('success') }}</div>@endif

    <div class="grid gap-4">
        @forelse($discussions as $discussion)
            <a href="{{ route($routePrefix.'.classes.discussions.show', [$class, $discussion]) }}" class="kt-card transition-colors hover:border-primary">
                <div class="kt-card-content flex items-start gap-4 p-5 lg:p-7.5">
                    <div class="flex size-11 shrink-0 items-center justify-center rounded-full bg-primary/10 font-semibold text-primary">
                        {{ str($discussion->author->name)->substr(0, 1)->upper() }}
                    </div>
                    <div class="min-w-0 grow">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div>
                                <h2 class="font-semibold text-mono hover:text-primary">{{ $discussion->title }}</h2>
                                <p class="mt-1 text-sm text-secondary-foreground">
                                    {{ $discussion->author->name }} · {{ $discussion->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <span class="kt-badge kt-badge-outline gap-1.5 shrink-0">
                                <i class="ki-filled ki-message-text"></i>{{ $discussion->comments_count }} komentar
                            </span>
                        </div>
                        <p class="mt-3 line-clamp-2 text-sm leading-6 text-foreground">{{ $discussion->body }}</p>
                    </div>
                </div>
            </a>
        @empty
            <div class="kt-card">
                <div class="kt-card-content flex flex-col items-center py-12 text-center">
                    <div class="flex size-14 items-center justify-center rounded-full bg-primary/10 text-primary">
                        <i class="ki-filled ki-message-text-2 text-2xl"></i>
                    </div>
                    <h2 class="mt-4 font-semibold text-mono">Belum ada topik diskusi</h2>
                    <p class="mt-1 text-sm text-secondary-foreground">Guru belum memulai diskusi untuk kelas ini.</p>
                </div>
            </div>
        @endforelse
    </div>

    @if($discussions->hasPages())
        <div class="flex flex-wrap items-center justify-between gap-3">
            <span class="text-sm text-secondary-foreground">Menampilkan {{ $discussions->firstItem() }}–{{ $discussions->lastItem() }} dari {{ $discussions->total() }} topik</span>
            {{ $discussions->links('vendor.pagination.compact') }}
        </div>
    @endif
</div>
@endsection
