{{-- layouts/partials/sidebar.blade.php — NFR-08, M0 shell (nav items wired in M1+) --}}
<div class="kt-sidebar bg-background border-e border-e-border fixed top-0 bottom-0 z-20 hidden lg:flex flex-col items-stretch shrink-0 [--kt-drawer-enable:true] lg:[--kt-drawer-enable:false]"
     data-kt-drawer="true"
     data-kt-drawer-class="kt-drawer kt-drawer-start top-0 bottom-0"
     id="sidebar">

    {{-- Sidebar Header: Logo + Collapse Toggle --}}
    <div class="kt-sidebar-header hidden lg:flex items-center relative justify-between px-3 lg:px-6 shrink-0" id="sidebar_header">
        <a class="dark:hidden" href="{{ url('/') }}">
            <img class="default-logo min-h-[22px] max-w-none" src="{{ asset('assets/media/app/default-logo.svg') }}" alt="{{ config('app.name') }}"/>
            <img class="small-logo min-h-[22px] max-w-none" src="{{ asset('assets/media/app/mini-logo.svg') }}" alt="{{ config('app.name') }}"/>
        </a>
        <a class="hidden dark:block" href="{{ url('/') }}">
            <img class="default-logo min-h-[22px] max-w-none" src="{{ asset('assets/media/app/default-logo-dark.svg') }}" alt="{{ config('app.name') }}"/>
            <img class="small-logo min-h-[22px] max-w-none" src="{{ asset('assets/media/app/mini-logo.svg') }}" alt="{{ config('app.name') }}"/>
        </a>
        <button class="kt-btn kt-btn-outline kt-btn-icon size-[30px] absolute start-full top-2/4 -translate-x-2/4 -translate-y-2/4 rtl:translate-x-2/4"
                data-kt-toggle="body"
                data-kt-toggle-class="kt-sidebar-collapse"
                id="sidebar_toggle">
            <i class="ki-filled ki-black-left-line kt-toggle-active:rotate-180 transition-all duration-300 rtl:translate rtl:rotate-180 rtl:kt-toggle-active:rotate-0"></i>
        </button>
    </div>

    {{-- Sidebar Scrollable Content --}}
    <div class="kt-sidebar-content flex grow shrink-0 py-5 pe-2" id="sidebar_content">
        <div class="kt-scrollable-y-hover grow shrink-0 flex ps-2 lg:ps-5 pe-1 lg:pe-3"
             data-kt-scrollable="true"
             data-kt-scrollable-dependencies="#sidebar_header"
             data-kt-scrollable-height="auto"
             data-kt-scrollable-offset="0px"
             data-kt-scrollable-wrappers="#sidebar_content"
             id="sidebar_scrollable">

            {{-- Sidebar Menu — role-based nav items injected in M1+ --}}
            <div class="kt-menu flex flex-col grow gap-1"
                 data-kt-menu="true"
                 data-kt-menu-accordion-expand-all="false"
                 id="sidebar_menu">

                {{-- Dashboard (placeholder — all roles) --}}
                <div class="kt-menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a class="kt-menu-link border border-transparent items-center grow
                              kt-menu-item-active:bg-accent/60 kt-menu-item-active:rounded-lg
                              hover:bg-accent/60 hover:rounded-lg gap-[10px] ps-[10px] pe-[10px] py-[8px]"
                       href="{{ url('/dashboard') }}">
                        <span class="kt-menu-icon items-start text-muted-foreground w-[20px]
                                     kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                            <i class="ki-filled ki-element-11 text-lg"></i>
                        </span>
                        <span class="kt-menu-title text-sm font-medium text-foreground
                                     kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                            Dashboard
                        </span>
                    </a>
                </div>

                {{-- Role-specific nav items will be @include'd here in M1 --}}
                @stack('sidebar_nav')

                {{-- Super Admin nav — FR-SA-02 / M2 --}}
                @role('super_admin')
                <div class="kt-menu-item pt-2">
                    <span class="kt-menu-heading text-xs font-semibold text-secondary-foreground uppercase tracking-wider ps-[10px]">Admin</span>
                </div>
                <div class="kt-menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <a class="kt-menu-link border border-transparent items-center grow
                              kt-menu-item-active:bg-accent/60 kt-menu-item-active:rounded-lg
                              hover:bg-accent/60 hover:rounded-lg gap-[10px] ps-[10px] pe-[10px] py-[8px]"
                       href="{{ route('admin.users.index') }}">
                        <span class="kt-menu-icon items-start text-muted-foreground w-[20px]">
                            <i class="ki-filled ki-profile-circle text-lg"></i>
                        </span>
                        <span class="kt-menu-title text-sm font-medium text-foreground
                                     kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                            Pengguna
                        </span>
                    </a>
                </div>
                <div class="kt-menu-item {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
                    <a class="kt-menu-link border border-transparent items-center grow
                              kt-menu-item-active:bg-accent/60 kt-menu-item-active:rounded-lg
                              hover:bg-accent/60 hover:rounded-lg gap-[10px] ps-[10px] pe-[10px] py-[8px]"
                       href="{{ route('admin.classes.index') }}">
                        <span class="kt-menu-icon items-start text-muted-foreground w-[20px]
                                     kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                            <i class="ki-filled ki-book-open text-lg"></i>
                        </span>
                        <span class="kt-menu-title text-sm font-medium text-foreground
                                     kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                            Kelas
                        </span>
                    </a>
                </div>
                <div class="kt-menu-item {{ request()->routeIs('admin.recap.*') ? 'active' : '' }}">
                    <a class="kt-menu-link border border-transparent items-center grow hover:bg-accent/60 hover:rounded-lg gap-[10px] ps-[10px] pe-[10px] py-[8px]" href="{{ route('admin.recap.index') }}"><span class="kt-menu-icon w-[20px]"><i class="ki-filled ki-chart-line text-lg"></i></span><span class="kt-menu-title text-sm font-medium text-foreground">Rekap Nilai</span></a>
                </div>
                @endrole

                @role('guru')
                <div class="kt-menu-item {{ request()->routeIs('guru.classes.*') ? 'active' : '' }}">
                    <a class="kt-menu-link border border-transparent items-center grow
                              kt-menu-item-active:bg-accent/60 kt-menu-item-active:rounded-lg
                              hover:bg-accent/60 hover:rounded-lg gap-[10px] ps-[10px] pe-[10px] py-[8px]"
                       href="{{ route('guru.classes.index') }}">
                        <span class="kt-menu-icon items-start text-muted-foreground w-[20px]
                                     kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                            <i class="ki-filled ki-book-open text-lg"></i>
                        </span>
                        <span class="kt-menu-title text-sm font-medium text-foreground
                                     kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                            Kelas Saya
                        </span>
                    </a>
                </div>
                @endrole

                @role('siswa')
                <div class="kt-menu-item {{ request()->routeIs('siswa.classes.*') ? 'active' : '' }}">
                    <a class="kt-menu-link border border-transparent items-center grow
                              kt-menu-item-active:bg-accent/60 kt-menu-item-active:rounded-lg
                              hover:bg-accent/60 hover:rounded-lg gap-[10px] ps-[10px] pe-[10px] py-[8px]"
                       href="{{ route('siswa.classes.index') }}">
                        <span class="kt-menu-icon items-start text-muted-foreground w-[20px]
                                     kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                            <i class="ki-filled ki-book-open text-lg"></i>
                        </span>
                        <span class="kt-menu-title text-sm font-medium text-foreground
                                     kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                            Kelas Saya
                        </span>
                    </a>
                </div>
                <div class="kt-menu-item {{ request()->routeIs('siswa.grades.*') ? 'active' : '' }}">
                    <a class="kt-menu-link border border-transparent items-center grow hover:bg-accent/60 hover:rounded-lg gap-[10px] ps-[10px] pe-[10px] py-[8px]" href="{{ route('siswa.grades.index') }}"><span class="kt-menu-icon w-[20px]"><i class="ki-filled ki-chart-line text-lg"></i></span><span class="kt-menu-title text-sm font-medium text-foreground">Nilai Saya</span></a>
                </div>
                @endrole

            </div>
            {{-- End Sidebar Menu --}}
        </div>
    </div>
</div>
