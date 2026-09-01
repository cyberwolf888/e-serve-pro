{{-- layouts/partials/header.blade.php — NFR-08, M0 shell (topbar wired in M1+) --}}
<header class="kt-header fixed top-0 z-10 start-0 end-0 flex items-stretch shrink-0 bg-background border-b border-border"
        data-kt-sticky="true"
        data-kt-sticky-name="header"
        id="header">

    <div class="kt-container-fixed flex justify-between items-stretch lg:gap-4" id="headerContainer">

        {{-- Mobile Logo + Menu Toggle --}}
        <div class="flex gap-2.5 lg:hidden items-center -ms-1">
            <a class="shrink-0" href="{{ url('/') }}" aria-label="{{ config('app.name') }}">
                <x-brand-logo icon-class="size-7" text-class="text-sm"/>
            </a>
            <div class="flex items-center">
                <button class="kt-btn kt-btn-icon kt-btn-ghost" data-kt-drawer-toggle="#sidebar">
                    <i class="ki-filled ki-menu"></i>
                </button>
            </div>
        </div>

        {{-- Breadcrumbs --}}
        @yield('breadcrumb')

        {{-- Topbar Right --}}
        <div class="flex items-center gap-2 lg:gap-3.5">

            {{-- Topbar: role-specific actions injected in M1+ --}}
            @stack('topbar_actions')

            {{-- User Menu stub — wired in M1 after auth exists --}}
            @auth
            <div class="shrink-0"
                 data-kt-dropdown="true"
                 data-kt-dropdown-offset="10px, 10px"
                 data-kt-dropdown-placement="bottom-end"
                 data-kt-dropdown-trigger="click">
                <div class="cursor-pointer shrink-0" data-kt-dropdown-toggle="true">
                    <div class="size-9 rounded-full bg-primary flex items-center justify-center text-white text-sm font-semibold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
                <div class="kt-dropdown-menu w-[200px]" data-kt-dropdown-menu="true">
                    <div class="flex items-center gap-2 px-2.5 py-2">
                        <div class="flex flex-col gap-1">
                            <span class="text-sm font-semibold text-foreground leading-none">
                                {{ auth()->user()->name }}
                            </span>
                            <span class="text-xs text-secondary-foreground leading-none">
                                {{ auth()->user()->email }}
                            </span>
                        </div>
                    </div>
                    <ul class="kt-dropdown-menu-sub">
                        <li><div class="kt-dropdown-menu-separator"></div></li>
                        <li>
                            <a class="kt-dropdown-menu-link" href="{{ route('profile.show') }}">
                                <i class="ki-filled ki-user"></i>
                                Profil Saya
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('auth.logout') }}">
                                @csrf
                                <button type="submit" class="kt-dropdown-menu-link w-full text-left">
                                    <i class="ki-filled ki-entrance-right"></i>
                                    Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            @endauth

        </div>
        {{-- End Topbar --}}

    </div>
</header>
