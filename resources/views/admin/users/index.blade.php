{{-- admin/users/index.blade.php — FR-SA-02 / BR-05 / NFR-08 / M2 --}}
@extends('layouts.app')

@section('content')
<div class="grid gap-5 lg:gap-7.5 py-6">

    {{-- Page header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-semibold text-mono">Manajemen Pengguna</h1>
            <p class="text-sm text-secondary-foreground mt-0.5">Kelola akun guru dan siswa.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="kt-btn kt-btn-primary">
            <i class="ki-filled ki-plus me-1.5"></i>
            Tambah Pengguna
        </a>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="kt-alert kt-alert-success flex items-center gap-2">
            <i class="ki-filled ki-check-circle text-success"></i>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="kt-alert kt-alert-destructive flex items-center gap-2">
            <i class="ki-filled ki-cross-circle text-destructive"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Users table --}}
    <div class="kt-card kt-card-grid min-w-full">
        <div class="kt-card-header py-5 flex items-center justify-between">
            <h3 class="kt-card-title">Daftar Pengguna</h3>
            <span class="text-sm text-secondary-foreground">{{ $users->total() }} pengguna</span>
        </div>
        <div class="kt-card-content">
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="text-left text-xs font-semibold text-secondary-foreground uppercase tracking-wider pb-3 min-w-[240px]">Nama</th>
                        <th class="text-left text-xs font-semibold text-secondary-foreground uppercase tracking-wider pb-3 min-w-[200px]">Email</th>
                        <th class="text-left text-xs font-semibold text-secondary-foreground uppercase tracking-wider pb-3 min-w-[100px]">Peran</th>
                        <th class="text-left text-xs font-semibold text-secondary-foreground uppercase tracking-wider pb-3 min-w-[110px]">Status</th>
                        <th class="text-left text-xs font-semibold text-secondary-foreground uppercase tracking-wider pb-3 min-w-[120px]">Dibuat oleh</th>
                        <th class="w-[60px]"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($users as $user)
                        <tr class="{{ $user->is_active ? '' : 'opacity-60' }}">
                            <td class="py-3">
                                <span class="font-medium text-sm text-mono">{{ $user->name }}</span>
                            </td>
                            <td class="py-3">
                                <span class="text-sm text-foreground">{{ $user->email }}</span>
                            </td>
                            <td class="py-3">
                                @php $role = $user->roles->first()?->name @endphp
                                @if ($role === 'guru')
                                    <span class="kt-badge kt-badge-outline kt-badge-info">Guru</span>
                                @elseif ($role === 'siswa')
                                    <span class="kt-badge kt-badge-outline kt-badge-secondary">Siswa</span>
                                @else
                                    <span class="kt-badge kt-badge-outline">—</span>
                                @endif
                            </td>
                            <td class="py-3">
                                @if ($user->is_active)
                                    <span class="kt-badge kt-badge-outline kt-badge-success">Aktif</span>
                                @else
                                    <span class="kt-badge kt-badge-outline kt-badge-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <span class="text-sm text-secondary-foreground">
                                    {{ $user->createdBy?->name ?? '—' }}
                                </span>
                            </td>
                            <td class="py-3 w-[60px]">
                                <div class="kt-menu" data-kt-menu="true">
                                    <div class="kt-menu-item"
                                         data-kt-menu-item-offset="0, 10px"
                                         data-kt-menu-item-placement="bottom-end"
                                         data-kt-menu-item-placement-rtl="bottom-start"
                                         data-kt-menu-item-toggle="dropdown"
                                         data-kt-menu-item-trigger="click">
                                        <button class="kt-menu-toggle kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost">
                                            <i class="ki-filled ki-dots-vertical text-lg"></i>
                                        </button>
                                        <div class="kt-menu-dropdown kt-menu-default w-full max-w-[175px]" data-kt-menu-dismiss="true">
                                            @if ($user->is_active)
                                                <div class="kt-menu-item">
                                                    <a class="kt-menu-link" href="{{ route('admin.users.edit', $user) }}">
                                                        <span class="kt-menu-icon"><i class="ki-filled ki-pencil"></i></span>
                                                        <span class="kt-menu-title">Edit</span>
                                                    </a>
                                                </div>
                                            @endif
                                            <div class="kt-menu-separator"></div>
                                            <div class="kt-menu-item">
                                                <form method="POST" action="{{ route('admin.users.status', $user) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="kt-menu-link w-full text-left
                                                        {{ $user->is_active ? 'text-destructive' : 'text-success' }}">
                                                        <span class="kt-menu-icon">
                                                            <i class="ki-filled {{ $user->is_active ? 'ki-minus-circle' : 'ki-check-circle' }}"></i>
                                                        </span>
                                                        <span class="kt-menu-title">
                                                            {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                        </span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-sm text-secondary-foreground">
                                Belum ada pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="kt-card-footer py-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

@push('sidebar_nav')
@endpush
