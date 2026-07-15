{{-- admin/users/create.blade.php — FR-SA-02 / FR-AUTH-03 / §9 / NFR-08 / M2 --}}
@extends('layouts.app')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Manajemen Pengguna', 'url' => route('admin.users.index')],
        ['label' => 'Tambah Pengguna'],
    ]" />
@endsection

@section('content')
<div class="grid gap-5 lg:gap-7.5 py-6 xl:w-[38.75rem] mx-auto">

    {{-- Page header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.users.index') }}" class="kt-btn kt-btn-ghost kt-btn-icon">
            <i class="ki-filled ki-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-mono">Tambah Pengguna</h1>
            <p class="text-sm text-secondary-foreground mt-0.5">Buat akun guru atau siswa baru.</p>
        </div>
    </div>

    {{-- Form card --}}
    <div class="kt-card">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="kt-card-content grid gap-5 p-7.5">
                @include('admin.users._form', ['user' => null, 'isEdit' => false])
            </div>
            <div class="kt-card-footer flex justify-end gap-2.5 py-5">
                <a href="{{ route('admin.users.index') }}" class="kt-btn kt-btn-outline">
                    <i class="ki-filled ki-cross"></i>
                    Batal
                </a>
                <button type="submit" class="kt-btn kt-btn-primary">
                    <i class="ki-filled ki-check"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
