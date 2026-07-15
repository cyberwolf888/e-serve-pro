{{-- admin/classes/index.blade.php — FR-SA-03 / NFR-08 / M3 --}}
@extends('layouts.app')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Kelas'],
    ]" />
@endsection

@section('content')
<div class="grid gap-5 lg:gap-7.5">

    {{-- Page header --}}
    <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-2">
        <div class="flex flex-col justify-center gap-2">
            <h1 class="text-xl font-medium leading-none text-mono">Semua Kelas</h1>
            <div class="flex items-center flex-wrap gap-1.5 font-medium">
                <span class="text-base text-secondary-foreground">Semua Kelas:</span>
                <span class="text-base text-foreground font-medium">{{ $classes->count() }}</span>
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.classes.create') }}" class="kt-btn kt-btn-primary">
                <i class="ki-filled ki-plus me-1.5"></i>
                Tambah Kelas
            </a>
        </div>
    </div>

    {{-- Flash messages — auto-dismiss after 5 seconds (NFR-08) --}}
    @if (session('success'))
        <div class="kt-alert kt-alert-success flex items-center gap-2" data-auto-dismiss>
            <i class="ki-filled ki-check-circle text-success"></i>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="kt-alert kt-alert-destructive flex items-center gap-2" data-auto-dismiss>
            <i class="ki-filled ki-cross-circle text-destructive"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Classes table --}}
    <div class="kt-card kt-card-grid min-w-full">

        {{-- Card header: search + filters --}}
        <div class="kt-card-header flex-wrap gap-2">
            <h3 class="kt-card-title text-sm">Menampilkan {{ $classes->count() }} kelas</h3>
            <div class="flex flex-wrap gap-2 lg:gap-5">
                <div class="flex">
                    <label class="kt-input">
                        <i class="ki-filled ki-magnifier"></i>
                        <input data-kt-datatable-search="#classes_table" placeholder="Cari kelas..." type="text" value="" />
                    </label>
                </div>
                <form class="flex flex-wrap gap-2.5" method="GET">
                    <select class="kt-select w-36" data-kt-select="true" data-kt-select-placeholder="Semua Status" name="status">
                        <option value="">Semua Status</option>
                        <option value="active" @selected(request('status') === 'active')>Aktif</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                    </select>
                    <select class="kt-select w-36" data-kt-select="true" data-kt-select-placeholder="Urutan" name="sort">
                        <option value="newest" @selected(request('sort', 'newest') === 'newest')>Terbaru</option>
                        <option value="oldest" @selected(request('sort') === 'oldest')>Terlama</option>
                    </select>
                    <button class="kt-btn kt-btn-outline kt-btn-primary" type="submit">
                        <i class="ki-filled ki-setting-4"></i>
                        Filter
                    </button>
                </form>
            </div>
        </div>

        {{-- Card content: datatable --}}
        <div class="kt-card-content">
            <div data-kt-datatable="true" data-kt-datatable-state-save="false" data-kt-datatable-page-size="10" id="classes_table">
                <div class="kt-scrollable-x-auto">
                    <table class="kt-table table-auto kt-table-border" data-kt-datatable-table="true">
                        <thead>
                            <tr>
                                <th class="min-w-[280px]">
                                    <span class="kt-table-col">
                                        <span class="kt-table-col-label">Kelas</span>
                                        <span class="kt-table-col-sort"></span>
                                    </span>
                                </th>
                                <th class="min-w-[160px]">
                                    <span class="kt-table-col">
                                        <span class="kt-table-col-label">Guru</span>
                                        <span class="kt-table-col-sort"></span>
                                    </span>
                                </th>
                                <th class="min-w-[120px]">
                                    <span class="kt-table-col">
                                        <span class="kt-table-col-label">Jumlah Siswa</span>
                                        <span class="kt-table-col-sort"></span>
                                    </span>
                                </th>
                                <th class="min-w-[140px]" data-field="status">
                                    <span class="kt-table-col">
                                        <span class="kt-table-col-label">Status</span>
                                        <span class="kt-table-col-sort"></span>
                                    </span>
                                </th>
                                {{-- Hidden column for date sort --}}
                                <th data-field="created_at" class="sr-only">Tanggal</th>
                                <th class="w-[60px]"></th>
                            </tr>
                        </thead>
                        <tbody>
                                @forelse ($classes as $class)
                                <tr>
                                    {{-- Kelas: name + code --}}
                                    <td>
                                        <div class="flex flex-col">
                                            <a class="text-sm font-medium text-mono hover:text-primary mb-px"
                                               href="{{ route('admin.classes.show', $class) }}">
                                                {{ $class->name }}
                                            </a>
                                            <span class="text-sm text-secondary-foreground font-normal">{{ $class->class_code }}</span>
                                        </div>
                                    </td>
                                    {{-- Guru --}}
                                    <td class="text-foreground font-normal">
                                        {{ $class->guru->name }}
                                    </td>
                                    {{-- Jumlah Siswa --}}
                                    <td class="text-foreground font-normal">
                                        {{ $class->members_count }}
                                    </td>
                                    {{-- Status: dot-badge --}}
                                    <td>
                                        @if ($class->is_active)
                                            <span class="kt-badge kt-badge-success kt-badge-outline rounded-[30px]">
                                                <span class="kt-badge-dot size-1.5"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="kt-badge kt-badge-danger kt-badge-outline rounded-[30px]">
                                                <span class="kt-badge-dot size-1.5"></span>
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    {{-- Hidden date cell for sort --}}
                                    <td class="sr-only">{{ $class->created_at->timestamp }}</td>
                                    {{-- Actions --}}
                                    <td>
                                        <div class="kt-menu flex-inline" data-kt-menu="true">
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
                                                    @if ($class->is_active)
                                                        <div class="kt-menu-item">
                                                            <a class="kt-menu-link" href="{{ route('admin.classes.edit', $class) }}">
                                                                <span class="kt-menu-icon"><i class="ki-filled ki-pencil"></i></span>
                                                                <span class="kt-menu-title">Edit</span>
                                                            </a>
                                                        </div>
                                                        <div class="kt-menu-separator"></div>
                                                        <div class="kt-menu-item">
                                                            <button type="button" class="kt-menu-link w-full text-left text-destructive"
                                                                    data-kt-modal-toggle="#confirm_status_modal"
                                                                    data-action="{{ route('admin.classes.destroy', $class) }}"
                                                                    data-method="DELETE"
                                                                    data-message="Nonaktifkan kelas {{ $class->name }}? Kelas menjadi hanya-baca."
                                                                    data-label="Nonaktifkan"
                                                                    data-variant="destructive">
                                                                <span class="kt-menu-icon"><i class="ki-filled ki-minus-circle"></i></span>
                                                                <span class="kt-menu-title">Nonaktifkan</span>
                                                            </button>
                                                        </div>
                                                    @else
                                                        <div class="kt-menu-item">
                                                            <button type="button" class="kt-menu-link w-full text-left"
                                                                    data-kt-modal-toggle="#confirm_status_modal"
                                                                    data-action="{{ route('admin.classes.activate', $class) }}"
                                                                    data-method="PATCH"
                                                                    data-message="Aktifkan kembali kelas {{ $class->name }}?"
                                                                    data-label="Aktifkan"
                                                                    data-variant="primary">
                                                                <span class="kt-menu-icon"><i class="ki-filled ki-check-circle"></i></span>
                                                                <span class="kt-menu-title">Aktifkan</span>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-sm text-secondary-foreground">
                                        Belum ada kelas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Datatable footer: per-page + pagination --}}
                <div class="kt-card-footer justify-center md:justify-between flex-col md:flex-row gap-5 text-secondary-foreground text-sm font-medium">
                    <div class="flex items-center gap-2 order-2 md:order-1">
                        Tampilkan
                        <select class="kt-select w-16" data-kt-datatable-size="true" data-kt-select="" name="perpage"></select>
                        per halaman
                    </div>
                    <div class="flex items-center gap-4 order-1 md:order-2">
                        <span data-kt-datatable-info="true"></span>
                        <div class="kt-datatable-pagination" data-kt-datatable-pagination="true"></div>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>

<x-confirm-status-modal />
@endsection

@push('scripts')
<script>
(function () {
    // Auto-dismiss flash alerts after 5 seconds
    document.querySelectorAll('[data-auto-dismiss]').forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 300ms ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
}());
</script>
@endpush
