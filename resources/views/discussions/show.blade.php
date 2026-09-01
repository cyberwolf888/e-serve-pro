{{-- discussions/show.blade.php — FR-SA-07 / FR-GR-14 / FR-SW-07 / NFR-08 / M7.8 --}}
@extends('layouts.app')
@php($indexLabel = $routePrefix === 'admin' ? 'Kelas' : 'Kelas Saya')
@section('breadcrumb')<x-breadcrumb :items="[['label' => $indexLabel, 'url' => route($routePrefix.'.classes.index')], ['label' => $class->name, 'url' => route($routePrefix.'.classes.show', $class)], ['label' => 'Diskusi', 'url' => route($routePrefix.'.classes.discussions.index', $class)], ['label' => $discussion->title]]" />@endsection
@section('content')
<div class="mx-auto grid max-w-4xl gap-5 lg:gap-7.5">
    <div class="flex items-center gap-3">
        <a href="{{ route($routePrefix.'.classes.discussions.index', $class) }}" class="kt-btn kt-btn-ghost kt-btn-icon"><i class="ki-filled ki-arrow-left text-lg"></i></a>
        <span class="text-sm font-medium text-secondary-foreground">Kembali ke Diskusi</span>
    </div>

    @if(session('success'))<div class="kt-alert kt-alert-success">{{ session('success') }}</div>@endif
    @if(! $class->is_active)<div class="kt-alert kt-alert-warning">Kelas nonaktif. Diskusi hanya dapat dibaca.</div>@endif

    <article class="kt-card">
        <div class="flex items-center gap-3 p-5 pb-0 lg:p-7.5 lg:pb-0">
            <div class="flex size-[50px] shrink-0 items-center justify-center rounded-full bg-primary/10 text-lg font-semibold text-primary">
                {{ str($discussion->author->name)->substr(0, 1)->upper() }}
            </div>
            <div>
                <p class="font-medium text-mono">{{ $discussion->author->name }}</p>
                <time class="text-sm text-secondary-foreground" datetime="{{ $discussion->created_at->toIso8601String() }}">{{ $discussion->created_at->translatedFormat('d M Y H:i') }}</time>
            </div>
        </div>
        <div class="grid gap-4 p-5 lg:p-7.5">
            <h1 class="text-2xl font-semibold text-mono">{{ $discussion->title }}</h1>
            <p class="whitespace-pre-line text-sm leading-6 text-foreground">{{ $discussion->body }}</p>
        </div>
        <div class="mx-5 flex items-center gap-2.5 border-y border-dashed border-input py-3 lg:mx-7.5">
            <span class="kt-btn bg-transparent px-3 text-secondary-foreground shadow-none">
                <i class="ki-filled ki-message-text"></i>{{ $comments->total() }} komentar
            </span>
        </div>

        <div class="grid gap-5 p-5 lg:p-7.5">
            @forelse($comments as $comment)
                <div class="flex items-start gap-3">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-muted font-semibold text-secondary-foreground">
                        {{ str($comment->author->name)->substr(0, 1)->upper() }}
                    </div>
                    <div class="min-w-0 grow">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                <span class="font-medium text-mono">{{ $comment->author->name }}</span>
                                <span class="kt-badge kt-badge-sm kt-badge-outline">{{ $comment->author->hasRole('guru') ? 'Guru' : 'Siswa' }}</span>
                                <time class="text-xs text-secondary-foreground" datetime="{{ $comment->created_at->toIso8601String() }}">{{ $comment->created_at->diffForHumans() }}</time>
                            </div>
                            @can('delete', $comment)
                                <button type="button" class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost text-destructive"
                                        aria-label="Hapus komentar"
                                        data-kt-modal-toggle="#confirm_status_modal"
                                        data-action="{{ route($routePrefix.'.classes.discussions.comments.destroy', [$class, $discussion, $comment]) }}"
                                        data-method="DELETE"
                                        data-message="Hapus komentar dari {{ $comment->author->name }}? Tindakan ini tidak dapat dibatalkan."
                                        data-label="Hapus"
                                        data-variant="destructive">
                                    <i class="ki-filled ki-trash"></i>
                                </button>
                            @endcan
                        </div>
                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-foreground">{{ $comment->body }}</p>
                    </div>
                </div>
            @empty
                <p class="py-4 text-center text-sm text-secondary-foreground">Belum ada komentar. Jadilah yang pertama menanggapi.</p>
            @endforelse
        </div>

        @if($comments->hasPages())
            <div class="kt-card-footer flex-wrap justify-between gap-3">
                <span class="text-sm text-secondary-foreground">Menampilkan {{ $comments->firstItem() }}–{{ $comments->lastItem() }} dari {{ $comments->total() }} komentar</span>
                {{ $comments->links('vendor.pagination.compact') }}
            </div>
        @endif

        @can('create', [App\Models\DiscussionComment::class, $discussion])
            <form method="POST" action="{{ route($routePrefix.'.classes.discussions.comments.store', [$class, $discussion]) }}" class="border-t border-border p-5 lg:p-7.5">
                @csrf
                <label class="kt-form-label mb-2" for="body">Tulis Komentar</label>
                <textarea id="body" name="body" rows="4" class="kt-textarea @error('body') border-destructive @enderror" maxlength="10000" placeholder="Bagikan pendapat atau pertanyaan Anda..." required>{{ old('body') }}</textarea>
                @error('body')<p class="mt-1 text-xs text-destructive">{{ $message }}</p>@enderror
                <div class="mt-3 flex justify-end">
                    <button class="kt-btn kt-btn-primary"><i class="ki-filled ki-send"></i>Kirim Komentar</button>
                </div>
            </form>
        @endcan
    </article>
</div>
<x-confirm-status-modal />
@endsection
