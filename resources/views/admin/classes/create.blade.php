{{-- admin/classes/create.blade.php — FR-SA-03 / NFR-08 / M3 --}}
@extends('layouts.app')
@section('title', 'Buat Kelas — '.config('app.name'))
@section('breadcrumb')<x-breadcrumb :items="[['label' => 'Kelas', 'url' => route('admin.classes.index')], ['label' => 'Tambah Kelas']]" />@endsection
@section('content')
    <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
        <div class="flex flex-col justify-center gap-2">
            <h1 class="text-xl font-medium leading-none text-mono">Buat Kelas</h1>
            <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">Tambahkan kelas baru dan tetapkan guru pengampu</div>
        </div>
        <a href="{{ route('admin.classes.index') }}" class="kt-btn kt-btn-outline">
            <i class="ki-filled ki-arrow-left"></i>
            Kembali ke Kelas
        </a>
    </div>

    <div class="grid gap-5 lg:gap-7.5 xl:w-[38.75rem] mx-auto">
        <div class="kt-card pb-2.5">
            <div class="kt-card-header">
                <h3 class="kt-card-title">Informasi Kelas</h3>
            </div>
            <form method="POST" action="{{ route('admin.classes.store') }}">
                @csrf
                <div class="kt-card-content grid gap-5">
                    @include('admin.classes._form', ['class' => null, 'searchableGuruSelect' => true])
                    <div class="flex justify-end gap-2.5">
                        <a href="{{ route('admin.classes.index') }}" class="kt-btn kt-btn-outline"><i class="ki-filled ki-cross"></i>Batal</a>
                        <button class="kt-btn kt-btn-primary" type="submit"><i class="ki-filled ki-check"></i>Simpan Kelas</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
