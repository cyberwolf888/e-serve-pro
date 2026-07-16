{{-- admin/users/create.blade.php — FR-SA-02 / FR-AUTH-03 / §9 / NFR-08 / M2 --}}
@extends('layouts.app')
@section('title', 'Buat Pengguna - '.config('app.name'))

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Manajemen Pengguna', 'url' => route('admin.users.index')],
        ['label' => 'Tambah Pengguna'],
    ]" />
@endsection

@section('content')
    <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
        <div class="flex flex-col justify-center gap-2">
            <h1 class="text-xl font-medium leading-none text-mono">Buat Pengguna</h1>
            <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">Buat akun guru atau siswa baru</div>
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
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="kt-card-content grid gap-5">
                    @include('admin.users._form', ['user' => null, 'isEdit' => false])
                    <div class="flex justify-end gap-2.5">
                        <a href="{{ route('admin.users.index') }}" class="kt-btn kt-btn-outline"><i class="ki-filled ki-cross"></i>Batal</a>
                        <button type="submit" class="kt-btn kt-btn-primary"><i class="ki-filled ki-check"></i>Simpan Pengguna</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
