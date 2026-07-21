{{-- home.blade.php — public landing page --}}
{{-- FR-PUB-01 --}}
@extends('layouts.public')

@section('title', config('app.name') . ' — Portal Pembelajaran Bahasa Indonesia')

@section('content')

{{-- Hero --}}
<section class="relative px-6 lg:px-16 py-16 lg:py-24 bg-muted/30 bg-[radial-gradient(theme(colors.primary/6)_1px,transparent_1px)] bg-[length:18px_18px]">
    <div class="grid lg:grid-cols-2 gap-12 items-center max-w-6xl mx-auto">
        <div class="flex flex-col gap-6">
            <h1 class="text-3xl lg:text-5xl font-semibold text-mono">
                Portal Pembelajaran Bahasa Indonesia berbasis
                <span class="text-primary">Kurikulum Merdeka</span>
            </h1>
            <p class="text-base lg:text-lg text-secondary-foreground max-w-lg">
                Satu platform untuk materi belajar, sesi pertemuan, presensi, kuis otomatis, dan rekap nilai —
                dirancang untuk guru dan siswa.
            </p>
            <a href="{{ route('auth.login.show') }}" class="kt-btn kt-btn-primary kt-btn-lg rounded-full w-fit">
                <i class="ki-outline ki-entrance-left"></i>
                <span>Masuk</span>
            </a>
        </div>
        <div class="flex flex-col gap-4">
            <div class="flex flex-wrap items-center justify-center gap-4">
                <span class="bg-white rounded-lg p-2 flex items-center"><img src="{{ asset('assets/media/logo-tut-wuri-handayani.jpeg') }}" alt="Tut Wuri Handayani" class="h-10 w-auto object-contain"></span>
                <span class="bg-white rounded-lg p-2 flex items-center"><img src="{{ asset('assets/media/logo-bima.jpeg') }}" alt="BIMA" class="h-10 w-auto object-contain"></span>
                <span class="bg-white rounded-lg p-2 flex items-center"><img src="{{ asset('assets/media/logo-undiksha.png') }}" alt="Undiksha" class="h-10 w-auto object-contain"></span>
            </div>
            {{-- ASSUMPTION: picsum.photos placeholder per user direction; swap for a real product screenshot later --}}
            <img
                src="{{ url('assets/media/images/600x400/siswa.jpg') }}"
                alt="Pratinjau platform PRO-BI SMART"
                class="rounded-2xl shadow-xl w-full transition-transform duration-300 hover:-translate-y-1 hover:shadow-2xl"
            >
        </div>
    </div>
</section>

{{-- Kurikulum Merdeka blurb --}}
<section id="tentang" class="px-6 lg:px-16 py-10 bg-muted/40">
    <div class="max-w-3xl mx-auto text-center">
        <h2 class="text-xl font-semibold text-mono mb-3">Selaras dengan Kurikulum Merdeka</h2>
        <p class="text-sm lg:text-base text-secondary-foreground">
            PRO-BI SMART disusun mengikuti prinsip pembelajaran mandiri dan berdiferensiasi pada Kurikulum
            Merdeka, memudahkan guru menyampaikan materi visual serta memantau perkembangan setiap siswa
            secara terukur.
        </p>
    </div>
</section>

{{-- Feature highlights --}}
<section id="fitur" class="px-6 lg:px-16 py-16">
    <h2 class="text-2xl font-semibold text-mono text-center mb-10">Fitur Utama</h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 max-w-6xl mx-auto">
        @foreach ([
            ['icon' => 'ki-book-open', 'title' => 'Materi Visual', 'desc' => 'Bagikan materi via tautan Figma atau unggah berkas PDF (maks. 20 MB).'],
            ['icon' => 'ki-calendar', 'title' => 'Sesi & Pertemuan', 'desc' => 'Kelola jadwal pertemuan per kelas beserta materi yang dibagikan.'],
            ['icon' => 'ki-check-circle', 'title' => 'Presensi', 'desc' => 'Catat kehadiran siswa per pertemuan: hadir, izin, sakit, atau alfa.'],
            ['icon' => 'ki-notepad-edit', 'title' => 'Kuis Otomatis', 'desc' => 'Kuis pilihan ganda dengan penilaian otomatis saat dikumpulkan.'],
            ['icon' => 'ki-chart-line', 'title' => 'Penilaian Berbobot', 'desc' => 'Hitung nilai akhir dari komponen penilaian berbobot secara manual.'],
            ['icon' => 'ki-shield-tick', 'title' => 'Monitoring Aktivitas', 'desc' => 'Pantau seluruh aktivitas pengguna: login, kuis, dan presensi.'],
        ] as $feature)
            <div class="kt-card">
                <div class="kt-card-body p-6 flex flex-col gap-3">
                    <i class="ki-filled {{ $feature['icon'] }} text-2xl text-primary"></i>
                    <h3 class="text-base font-medium text-mono">{{ $feature['title'] }}</h3>
                    <p class="text-sm text-secondary-foreground">{{ $feature['desc'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- Role explainer --}}
<section class="px-6 lg:px-16 py-16 bg-muted/40">
    <h2 class="text-2xl font-semibold text-mono text-center mb-10">Untuk Setiap Peran</h2>
    <div class="grid sm:grid-cols-3 gap-5 max-w-5xl mx-auto">
        @foreach ([
            ['icon' => 'ki-shield-tick', 'title' => 'Super Admin (Peneliti)', 'desc' => 'Mengelola akun pengguna, memantau seluruh aktivitas, dan mengakses rekap nilai lintas kelas.'],
            ['icon' => 'ki-profile-circle', 'title' => 'Guru', 'desc' => 'Mengelola kelas, materi, pertemuan, presensi, kuis, dan penilaian siswa.'],
            ['icon' => 'ki-people', 'title' => 'Siswa', 'desc' => 'Bergabung ke kelas dengan kode kelas, mengakses materi, mengerjakan kuis, dan melihat nilai.'],
        ] as $role)
            <div class="kt-card">
                <div class="kt-card-body p-6 flex flex-col gap-3 text-center items-center">
                    <i class="ki-filled {{ $role['icon'] }} text-2xl text-primary"></i>
                    <h3 class="text-base font-medium text-mono">{{ $role['title'] }}</h3>
                    <p class="text-sm text-secondary-foreground">{{ $role['desc'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>

@endsection
