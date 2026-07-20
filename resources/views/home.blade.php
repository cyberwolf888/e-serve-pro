{{-- home.blade.php — public landing page --}}
{{-- FR-PUB-01 --}}
@extends('layouts.public')

@section('title', config('app.name') . ' — Portal Pembelajaran Bahasa Indonesia')

@section('content')

{{-- Hero --}}
<section class="px-6 lg:px-16 py-16 lg:py-24 text-center flex flex-col items-center gap-6">
    <h1 class="text-3xl lg:text-5xl font-semibold text-mono max-w-3xl">
        Portal Pembelajaran Bahasa Indonesia berbasis <span class="text-primary">Kurikulum Merdeka</span>
    </h1>
    <p class="text-base lg:text-lg text-secondary-foreground max-w-2xl">
        Satu platform untuk materi belajar, sesi pertemuan, presensi, kuis otomatis, dan rekap nilai —
        dirancang untuk guru dan siswa.
    </p>
    <div class="flex items-center gap-3">
        <a href="{{ route('auth.register.show') }}" class="kt-btn kt-btn-primary kt-btn-lg">Daftar sebagai Siswa</a>
        <a href="{{ route('auth.login.show') }}" class="kt-btn kt-btn-outline kt-btn-lg">Masuk</a>
    </div>
</section>

{{-- Kurikulum Merdeka blurb --}}
<section class="px-6 lg:px-16 py-10 bg-muted/40">
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
<section class="px-6 lg:px-16 py-16">
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
