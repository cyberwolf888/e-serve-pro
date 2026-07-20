# Panduan Pengguna PRO-BI SMART

## 1. Tentang Aplikasi

PRO-BI SMART adalah platform pembelajaran online berbasis web yang mendukung pembelajaran sesuai Kurikulum Merdeka. Fitur yang tersedia meliputi:

- Manajemen akun berdasarkan peran.
- Manajemen kelas dan anggota kelas.
- Materi pembelajaran melalui tautan Figma atau berkas PDF.
- Jadwal pertemuan dan absensi.
- Kuis pilihan ganda dengan penilaian otomatis.
- Komponen nilai, nilai akhir, rekap, dan ekspor XLSX.

Platform memiliki tiga peran pengguna:

| Peran | Kegunaan |
| --- | --- |
| **Super Admin** | Mengelola pengguna, seluruh kelas, dan rekap nilai. |
| **Guru** | Mengelola kelas yang dimiliki, materi, pertemuan, kuis, absensi, dan nilai. |
| **Siswa** | Bergabung ke kelas, membaca materi, mengikuti pertemuan, mengerjakan kuis, dan melihat nilai sendiri. |

## 2. Memulai Penggunaan

### 2.1 Masuk

1. Buka halaman aplikasi.
2. Masukkan **Email** dan **Kata Sandi**.
3. Pilih **Ingat saya** jika ingin sesi login diingat oleh peramban.
4. Klik **Masuk**.

Setelah berhasil masuk, Anda diarahkan ke dashboard sesuai peran:

- Super Admin: **Dashboard Super Admin**.
- Guru: **Dashboard Guru**.
- Siswa: **Dashboard Siswa**.

Akun yang sudah dinonaktifkan tidak dapat digunakan untuk masuk.

### 2.2 Pendaftaran Siswa

Pendaftaran mandiri hanya tersedia untuk siswa.

1. Pada halaman masuk, klik **Daftar**.
2. Isi **Nama Lengkap**, **Email**, **Kata Sandi**, dan **Konfirmasi Kata Sandi**.
3. Gunakan email yang belum terdaftar.
4. Gunakan kata sandi minimal 8 karakter.
5. Klik **Daftar**.

Akun baru otomatis dibuat sebagai **Siswa** dan Anda langsung diarahkan ke dashboard siswa. Akun Guru tidak dapat dibuat melalui pendaftaran umum.

### 2.3 Lupa atau Mengatur Ulang Kata Sandi

1. Pada halaman masuk, klik **Lupa kata sandi?**.
2. Masukkan email akun.
3. Klik **Kirim Tautan Reset**.
4. Buka email yang diterima dan ikuti tautan reset.
5. Masukkan kata sandi baru dan konfirmasinya.
6. Klik **Simpan Kata Sandi**, lalu masuk kembali.

### 2.4 Keluar

Gunakan menu **Keluar** pada area akun untuk mengakhiri sesi. Selalu keluar setelah selesai menggunakan aplikasi, terutama pada komputer bersama.

## 3. Panduan Super Admin

Super Admin memiliki akses ke seluruh data pengguna, kelas, komponen nilai, dan rekap.

### 3.1 Membuat Pengguna

1. Buka menu **Pengguna**.
2. Klik **Tambah Pengguna**.
3. Isi nama lengkap dan email.
4. Pilih peran **Guru** atau **Siswa**.
5. Masukkan kata sandi minimal 8 karakter dan konfirmasinya.
6. Klik **Simpan Pengguna**.

Super Admin tidak membuat akun Super Admin dari formulir tersebut. Guru hanya dapat dibuat oleh Super Admin.

### 3.2 Mengubah atau Menonaktifkan Pengguna

1. Buka menu **Pengguna**.
2. Gunakan pencarian atau filter **Aktif/Nonaktif** bila diperlukan.
3. Buka menu tindakan pada pengguna.
4. Pilih **Edit** untuk mengubah nama, email, atau kata sandi.
5. Pilih **Nonaktifkan** untuk menonaktifkan akun.
6. Untuk akun nonaktif, pilih **Aktifkan** jika akses perlu dipulihkan.

Menonaktifkan pengguna tidak menghapus data yang sudah ada. Data yang terkait tetap tersimpan dan menjadi hanya-baca. Pengguna nonaktif juga tidak dapat masuk.

### 3.3 Mengelola Kelas

1. Buka menu **Kelas**.
2. Klik **Tambah Kelas**.
3. Pilih Guru pengampu.
4. Isi nama kelas dan, bila perlu, deskripsi.
5. Klik **Simpan Kelas**.

Sistem membuat kode kelas secara otomatis. Kode ini dapat digunakan siswa untuk bergabung.

Untuk mengubah kelas, buka kelas lalu klik **Edit**. Untuk menonaktifkan kelas, gunakan menu tindakan dan konfirmasi. Kelas nonaktif menjadi hanya-baca dan dapat diaktifkan kembali.

### 3.4 Melihat Rekap dan Mengunduh XLSX

1. Buka menu **Rekap Nilai**.
2. Daftar seluruh kelas akan ditampilkan.
3. Klik **Lihat Rekap** pada kelas tertentu untuk melihat detail nilai.
4. Klik **Unduh XLSX** untuk mengunduh rekap satu kelas.
5. Klik **Unduh XLSX** pada halaman **Rekap Semua Kelas** untuk mengunduh seluruh rekap.

## 4. Panduan Guru

Guru hanya dapat mengelola kelas yang dimiliki atau ditugaskan kepadanya.

### 4.1 Membuat Kelas

1. Buka menu **Kelas Saya**.
2. Klik **Tambah Kelas**.
3. Isi nama kelas dan deskripsi bila diperlukan.
4. Klik **Simpan Kelas**.
5. Salin **Kode Kelas** dan bagikan kepada siswa.

Tidak ada batas jumlah kelas yang dapat dibuat Guru atau jumlah siswa dalam satu kelas.

### 4.2 Menambahkan Siswa dengan Email

1. Buka kelas dari menu **Kelas Saya**.
2. Pada bagian **Siswa Terdaftar**, masukkan email siswa aktif.
3. Klik **Tambah**.

Siswa harus sudah memiliki akun aktif dengan peran Siswa. Jika email tidak ditemukan atau siswa sudah tidak aktif, penambahan akan ditolak.

### 4.3 Mengelola Materi

1. Buka kelas.
2. Pilih tab **Materi**.
3. Klik **Tambah Materi**.
4. Isi **Judul Materi**.
5. Pilih salah satu jenis materi:
   - **Tautan Figma**: masukkan URL Figma yang valid.
   - **Unggah PDF**: pilih berkas PDF.
6. Klik **Simpan**.

Ketentuan berkas:

- Hanya format PDF yang diterima.
- Ukuran maksimum 20 MB per berkas.
- Berkas selain PDF atau berkas yang terlalu besar akan ditolak.

Untuk mengubah materi, klik ikon edit. Untuk menghapus materi, klik ikon hapus dan konfirmasi. Materi yang sudah dibagikan ke pertemuan sebaiknya diperiksa kembali setelah diubah.

### 4.4 Membuat Pertemuan dan Membagikan Materi

1. Buka kelas dan pilih tab **Pertemuan**.
2. Klik **Tambah Pertemuan**.
3. Isi **Judul Pertemuan**, **Jadwal**, dan catatan bila diperlukan.
4. Klik **Simpan**.
5. Buka pertemuan yang dibuat.
6. Pada bagian **Bagikan Materi**, centang materi yang ingin diberikan kepada siswa.
7. Klik **Simpan**.

Siswa hanya melihat materi yang dibagikan pada pertemuan di kelas yang sudah diikuti.

### 4.5 Mencatat Absensi

1. Buka pertemuan.
2. Klik **Catat Absensi**.
3. Pilih status untuk setiap siswa:
   - **Hadir**
   - **Izin**
   - **Sakit**
   - **Alfa**
4. Klik **Simpan Absensi**.

Absensi yang disimpan dapat diperbarui melalui halaman yang sama.

### 4.6 Membuat Kuis

1. Buka kelas dan pilih tab **Kuis**.
2. Klik **Tambah Kuis**.
3. Isi judul dan deskripsi bila diperlukan.
4. Atur waktu **Dibuka Pada** dan **Ditutup Pada** bila kuis memiliki jadwal khusus. Kosongkan jika kuis langsung tersedia setelah diterbitkan dan tidak memiliki batas waktu.
5. Klik **Simpan**.
6. Buka kuis, lalu klik **Tambah Soal**.
7. Isi pertanyaan.
8. Masukkan minimal dua pilihan jawaban.
9. Pilih tepat satu jawaban benar menggunakan radio button.
10. Klik **Simpan**.
11. Ulangi untuk soal lainnya.
12. Klik **Publikasikan** jika kuis sudah siap dikerjakan.

Kuis yang sudah diterbitkan atau sudah memiliki percobaan siswa terkunci untuk perubahan soal. Jika perlu mengubah soal yang belum dikerjakan, batalkan publikasi terlebih dahulu. Kuis yang sudah pernah dikerjakan tidak dapat diubah.

### 4.7 Mengatur Komponen dan Bobot Nilai

1. Buka kelas dan pilih tab **Nilai**.
2. Pada bagian **Tambah Komponen**, masukkan nama komponen, misalnya `Kuis 1`, `Tugas`, atau `UAS`.
3. Masukkan bobot dalam persen.
4. Pilih kuis sebagai sumber jika komponen tersebut mengambil nilai dari kuis. Pilih **Input manual** untuk komponen non-kuis.
5. Klik **Tambah**.

Total bobot sebaiknya 100%. Jika tidak 100%, aplikasi menampilkan peringatan dan menormalisasi perhitungan berdasarkan total bobot saat nilai akhir dihitung.

### 4.8 Mengisi Nilai Komponen

1. Pada halaman **Nilai**, klik **Nilai** pada komponen yang ingin diisi.
2. Masukkan nilai setiap siswa dalam rentang 0 sampai 100.
3. Klik **Simpan Nilai**.

Nilai kuis yang dikaitkan ke komponen dapat terisi otomatis setelah siswa mengerjakan kuis. Nilai manual yang dimasukkan Guru menjadi pengganti dan tidak ditimpa oleh sinkronisasi otomatis.

### 4.9 Menghitung Nilai Akhir dan Rekap Kelas

1. Pada halaman **Komponen Nilai**, klik **Rekap Nilai**.
2. Periksa nilai setiap komponen.
3. Klik **Hitung Nilai Akhir**.
4. Periksa kolom **Nilai Akhir**.
5. Klik **Unduh XLSX** bila perlu mengunduh rekap kelas.

Nilai akhir dihitung dari nilai komponen dan bobotnya. Siswa nonaktif tidak diproses untuk nilai akhir baru.

## 5. Panduan Siswa

### 5.1 Bergabung ke Kelas dengan Kode

1. Buka menu **Kelas Saya**.
2. Masukkan kode kelas pada kolom **Kode kelas**.
3. Klik **Gabung Kelas**.
4. Buka kartu kelas untuk melihat isinya.

Keanggotaan langsung aktif tanpa persetujuan Guru. Kode kelas tidak membedakan huruf besar dan kecil. Jika sudah menjadi anggota, penggabungan ulang akan ditolak.

### 5.2 Mengakses Pertemuan dan Materi

1. Buka menu **Kelas Saya**.
2. Klik kelas yang diikuti.
3. Lihat bagian **Pertemuan**.
4. Baca jadwal dan catatan pertemuan.
5. Klik materi Figma untuk membukanya di tab baru.
6. Klik materi PDF untuk mengunduhnya.

Siswa tidak dapat melihat materi dari kelas yang belum diikuti. Kelas nonaktif tetap dapat dibaca, tetapi kontennya tidak dapat diubah.

### 5.3 Mengerjakan Kuis

1. Buka kelas yang diikuti.
2. Pada bagian **Kuis Tersedia**, klik **Kerjakan**.
3. Baca setiap pertanyaan.
4. Pilih satu jawaban pada setiap soal.
5. Klik **Kirim Jawaban**.
6. Konfirmasi pengiriman.

Semua soal wajib dijawab. Setiap kuis hanya dapat dikerjakan satu kali. Setelah dikirim, jawaban dinilai otomatis dengan skor 0 sampai 100 dan hasilnya dicatat pada sistem.

Kuis hanya dapat dibuka jika sudah diterbitkan, siswa merupakan anggota kelas, dan waktu kuis masih berada dalam jadwal yang ditentukan.

### 5.4 Melihat Nilai

1. Buka menu **Nilai Saya** untuk melihat seluruh nilai akhir.
2. Dari **Dashboard Siswa**, bagian **Nilai Terbaru** menampilkan hingga tiga nilai terbaru.
3. Periksa nama kelas, Guru, nilai akhir, dan waktu perhitungan.

Siswa hanya dapat melihat nilai miliknya sendiri.

## 6. Batasan dan Ketentuan Penting

- Akun Guru dibuat oleh Super Admin; Guru tidak dapat mendaftar mandiri.
- Siswa bergabung dengan kode kelas tanpa persetujuan Guru.
- Email harus unik di seluruh aplikasi.
- Berkas materi hanya PDF dengan ukuran maksimal 20 MB.
- Satu siswa hanya memiliki satu percobaan untuk setiap kuis.
- Semua soal kuis wajib dijawab sebelum dikirim.
- Tepat satu pilihan jawaban harus ditandai sebagai jawaban benar pada setiap soal.
- Data pengguna atau kelas yang dinonaktifkan tidak dihapus permanen dan menjadi hanya-baca.
- Tidak ada batas jumlah kelas per Guru atau jumlah siswa per kelas.
- Hak akses ditentukan berdasarkan peran; menu atau halaman yang tidak sesuai peran tidak dapat digunakan.

## 7. Pemecahan Masalah

### Tidak dapat masuk

- Periksa email dan kata sandi.
- Pastikan akun belum dinonaktifkan.
- Gunakan **Lupa kata sandi?** jika kata sandi tidak diingat.

### Tidak dapat bergabung ke kelas

- Periksa kembali kode kelas.
- Pastikan kelas masih aktif dan kode benar.
- Pastikan Anda belum menjadi anggota kelas tersebut.

### Materi PDF ditolak

- Pastikan berkas benar-benar berformat PDF.
- Pastikan ukuran berkas tidak lebih dari 20 MB.
- Coba unggah ulang berkas yang valid.

### Kuis tidak tersedia

- Pastikan Guru sudah menerbitkan kuis.
- Periksa apakah waktu buka kuis sudah tiba.
- Periksa apakah batas waktu kuis sudah lewat.
- Pastikan Anda anggota kelas.
- Pastikan kuis belum pernah dikerjakan.

### Nilai akhir belum muncul

- Guru harus mengisi atau memastikan nilai komponen tersedia.
- Guru harus menjalankan **Hitung Nilai Akhir** pada halaman rekap.
- Jika komponen berasal dari kuis, pastikan kuis sudah dikerjakan dan komponen dikaitkan dengan kuis tersebut.

## 8. Fitur yang Belum Tersedia pada Versi Ini

Halaman monitoring aktivitas khusus untuk Super Admin beserta filter log masih dalam tahap pengembangan. Aktivitas login, kuis, dan absensi tetap dicatat oleh sistem, tetapi belum tersedia sebagai menu monitoring khusus pada antarmuka pengguna versi ini.
