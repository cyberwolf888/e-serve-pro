{{-- admin/users/edit.blade.php — FR-SA-02 / BR-05 / §9 / NFR-08 / M2 --}}
@extends('layouts.app')
@section('title', 'Edit Pengguna - '.config('app.name'))

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Manajemen Pengguna', 'url' => route('admin.users.index')],
        ['label' => 'Edit Pengguna'],
    ]" />
@endsection

@section('content')
    <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
        <div class="flex flex-col justify-center gap-2">
            <h1 class="text-xl font-medium leading-none text-mono">Edit Pengguna</h1>
            <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                {{ $user->name }}
                @php $role = $user->roles->first()?->name @endphp
                @if ($role === 'guru')
                    <span class="kt-badge kt-badge-outline kt-badge-info kt-badge-sm ms-1">Guru</span>
                @elseif ($role === 'siswa')
                    <span class="kt-badge kt-badge-outline kt-badge-secondary kt-badge-sm ms-1">Siswa</span>
                @endif
            </div>
        </div>
        <a href="{{ route('admin.users.index') }}" class="kt-btn kt-btn-outline">
            <i class="ki-filled ki-arrow-left"></i>
            Kembali ke Pengguna
        </a>
    </div>

    <div class="grid gap-5 lg:gap-7.5 xl:w-[38.75rem] mx-auto">
        <div class="kt-card pb-2.5">
            <div class="kt-card-header">
                <h3 class="kt-card-title">Informasi Pengguna</h3>
            </div>
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')
                <div class="kt-card-content grid gap-5">
                    @include('admin.users._form', ['isEdit' => true])
                    <div class="flex justify-end gap-2.5">
                        <a href="{{ route('admin.users.index') }}" class="kt-btn kt-btn-outline"><i class="ki-filled ki-cross"></i>Batal</a>
                        <button type="submit" class="kt-btn kt-btn-primary"><i class="ki-filled ki-check"></i>Perbarui Pengguna</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
