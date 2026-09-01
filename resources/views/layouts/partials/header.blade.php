{{-- NFR-08 — Metronic Demo 2 authenticated header --}}
<header
    class="flex items-center transition-[height] shrink-0 bg-background h-(--header-height)"
    data-kt-sticky="true"
    data-kt-sticky-class="transition-[height] fixed z-10 top-0 left-0 right-0 backdrop-blur-md bg-background/70 border-b border-border"
    data-kt-sticky-name="header"
    data-kt-sticky-offset="200px"
    id="header"
>
    <div class="kt-container-fixed flex justify-between items-center lg:gap-4" id="headerContainer">
        <div class="flex items-center gap-2 lg:gap-5 2xl:-ml-[60px]">
            <a href="{{ url('/') }}" aria-label="{{ config('app.name') }}">
                <x-brand-logo icon-class="size-[42px]" text-class="text-base hidden sm:inline"/>
            </a>
            <div class="hidden md:flex items-center">
                <span class="text-sm text-muted-foreground font-medium px-2.5">/</span>
                <span class="text-mono text-sm font-medium">Portal Pembelajaran</span>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            @stack('topbar_actions')

            @auth
                <div
                    data-kt-dropdown="true"
                    data-kt-dropdown-offset="10px, 10px"
                    data-kt-dropdown-offset-rtl="-20px, 10px"
                    data-kt-dropdown-placement="bottom-end"
                    data-kt-dropdown-placement-rtl="bottom-start"
                    data-kt-dropdown-trigger="click"
                >
                    <button class="cursor-pointer shrink-0" type="button" data-kt-dropdown-toggle="true" aria-label="Buka menu pengguna">
                        <span class="size-9 rounded-full bg-primary flex items-center justify-center text-white text-sm font-semibold border-2 border-input">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                    </button>

                    <div class="kt-dropdown-menu w-[250px]" data-kt-dropdown-menu="true">
                        <div class="flex items-center px-2.5 py-1.5 gap-2">
                            <span class="size-9 shrink-0 rounded-full bg-primary flex items-center justify-center text-white text-sm font-semibold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <div class="flex min-w-0 flex-col gap-1.5">
                                <span class="truncate text-sm text-foreground font-semibold leading-none">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs text-secondary-foreground font-medium leading-none">{{ auth()->user()->email }}</span>
                            </div>
                        </div>

                        <ul class="kt-dropdown-menu-sub">
                            <li><div class="kt-dropdown-menu-separator"></div></li>
                            <li>
                                <a class="kt-dropdown-menu-link" href="{{ route('profile.show') }}">
                                    <i class="ki-filled ki-profile-circle"></i>
                                    Profil Saya
                                </a>
                            </li>
                        </ul>

                        <div class="px-2.5 pt-1.5 mb-2.5 flex flex-col gap-3.5">
                            <div class="flex items-center gap-2 justify-between">
                                <span class="flex items-center gap-2">
                                    <i class="ki-filled ki-moon text-base text-muted-foreground"></i>
                                    <span class="font-medium text-sm">Mode Gelap</span>
                                </span>
                                <input class="kt-switch" data-kt-theme-switch-state="dark" data-kt-theme-switch-toggle="true" type="checkbox" aria-label="Aktifkan mode gelap"/>
                            </div>
                            <form method="POST" action="{{ route('auth.logout') }}">
                                @csrf
                                <button type="submit" class="kt-btn kt-btn-outline justify-center w-full">
                                    <i class="ki-filled ki-entrance-right"></i>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</header>
