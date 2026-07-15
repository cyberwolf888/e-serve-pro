{{-- guru/classes/show.blade.php — FR-GR-02 / FR-GR-03 / NFR-08 / M3 --}}
@extends('layouts.app')
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Kelas Saya', 'url' => route('guru.classes.index')], ['label' => $class->name]]" />@endsection
@section('content')
<div class="grid gap-5 lg:gap-7.5">
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('guru.classes.index') }}" class="kt-btn kt-btn-ghost kt-btn-icon"><i class="ki-filled ki-arrow-left text-lg"></i></a>
            <h1 class="text-xl font-semibold text-mono">{{ $class->name }}</h1>
        </div>
        @if($class->is_active)
        <div class="flex items-center gap-2.5">
            <a href="{{ route('guru.classes.edit', $class) }}" class="kt-btn kt-btn-outline kt-btn-primary"><i class="ki-filled ki-pencil"></i>Edit</a>
            <button type="button" class="kt-btn kt-btn-destructive"
                    data-kt-modal-toggle="#confirm_status_modal"
                    data-action="{{ route('guru.classes.destroy', $class) }}"
                    data-method="DELETE"
                    data-message="Nonaktifkan kelas {{ $class->name }}? Kelas menjadi hanya-baca."
                    data-label="Nonaktifkan"
                    data-variant="destructive">
                <i class="ki-filled ki-minus-circle"></i>Nonaktifkan Kelas
            </button>
        </div>
        @endif
    </div>
    @include('guru.classes._tabs', ['class' => $class])
    @if(session('success'))<div class="kt-alert kt-alert-success">{{ session('success') }}</div>@endif

    <div class="kt-card">
        <div class="kt-card-header"><h3 class="kt-card-title text-sm">Info Kelas</h3></div>
        <div class="kt-card-table kt-scrollable-x-auto pb-3">
            <table class="kt-table align-middle text-sm text-muted-foreground">
                <tr><td class="py-2 min-w-36 text-secondary-foreground font-normal">Kode Kelas</td><td class="py-2 text-foreground font-normal">{{ $class->class_code }}</td></tr>
                <tr><td class="py-2 text-secondary-foreground font-normal">Deskripsi</td><td class="py-2 text-foreground font-normal">{{ $class->description ?: '—' }}</td></tr>
                <tr>
                    <td class="py-2 text-secondary-foreground font-normal">Status</td>
                    <td class="py-2">
                        @if($class->is_active)
                            <span class="kt-badge kt-badge-success kt-badge-outline rounded-[30px]"><span class="kt-badge-dot size-1.5"></span>Aktif</span>
                        @else
                            <span class="kt-badge kt-badge-danger kt-badge-outline rounded-[30px]"><span class="kt-badge-dot size-1.5"></span>Nonaktif</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="kt-card">
        <div class="kt-card-header"><h3 class="kt-card-title text-sm">Siswa Terdaftar ({{ $members->total() }})</h3></div>
        @if($class->is_active)
        <div class="kt-card-content pb-0">
            <form method="POST" action="{{ route('guru.classes.students.store', $class) }}" class="flex items-start gap-2.5">
                @csrf
                <input name="email" type="email" class="kt-input grow @error('email') border-destructive @enderror" placeholder="email siswa" required />
                <button class="kt-btn kt-btn-outline kt-btn-primary shrink-0"><i class="ki-filled ki-plus"></i>Tambah</button>
            </form>
            @error('email')<p class="text-destructive text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        @endif
        <div class="kt-card-content p-0">
            <table class="kt-table table-auto kt-table-border">
                <thead>
                    <tr>
                        <th class="min-w-[200px]">Nama</th>
                        <th class="min-w-[220px]">Email</th>
                        <th class="min-w-[160px]">Tanggal Gabung</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                    <tr>
                        <td>{{ $member->student->name }}</td>
                        <td class="text-secondary-foreground">{{ $member->student->email }}</td>
                        <td class="text-secondary-foreground">{{ $member->joined_at->translatedFormat('d M Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="py-8 text-center text-sm text-secondary-foreground">Belum ada siswa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($members->hasPages())
        <div class="kt-card-footer justify-center">{{ $members->links() }}</div>
        @endif
    </div>
</div>
<x-confirm-status-modal />
@endsection
