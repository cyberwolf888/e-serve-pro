{{-- admin/users/edit.blade.php — FR-SA-02 / BR-05 / §9 / NFR-08 / M2 --}}
@extends('layouts.app')

@section('content')
<div class="grid gap-5 lg:gap-7.5 py-6 xl:w-[38.75rem] mx-auto">

    {{-- Page header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.users.index') }}" class="kt-btn kt-btn-ghost kt-btn-icon">
            <i class="ki-filled ki-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-mono">Edit Pengguna</h1>
            <p class="text-sm text-secondary-foreground mt-0.5">
                {{ $user->name }}
                @php $role = $user->roles->first()?->name @endphp
                @if ($role === 'guru')
                    <span class="kt-badge kt-badge-outline kt-badge-info kt-badge-sm ms-1">Guru</span>
                @elseif ($role === 'siswa')
                    <span class="kt-badge kt-badge-outline kt-badge-secondary kt-badge-sm ms-1">Siswa</span>
                @endif
            </p>
        </div>
    </div>

    {{-- Form card --}}
    <div class="kt-card">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')
            <div class="kt-card-content grid gap-5 p-7.5">
                @include('admin.users._form', ['isEdit' => true])
            </div>
            <div class="kt-card-footer flex justify-end gap-2.5 py-5">
                <a href="{{ route('admin.users.index') }}" class="kt-btn kt-btn-outline">Batal</a>
                <button type="submit" class="kt-btn kt-btn-primary">Perbarui</button>
            </div>
        </form>
    </div>

</div>
@endsection
