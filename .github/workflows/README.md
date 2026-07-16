# Konfigurasi GitHub Secrets dan Variables

Repository ini memerlukan konfigurasi secrets dan variables di GitHub untuk menjalankan workflow deployment.

## Variables (Repository / Environment Levels)

| Variable | Contoh Nilai | Keterangan |
|----------|--------------|------------|
| `DOCKERHUB_IMAGE` | `yourusername/reporaksi-event` | Nama image Docker Hub lengkap dengan username/organisasi |
| `CONTAINER_NAME` | `reporaksi-event` | Nama container yang akan dijalankan |
| `APP_DIR` | `/home/deploy/reporaksi-event` | Direktori aplikasi di VM. Jika tidak diisi, workflow fallback ke `$HOME/reporaksi-event` |
| `HOST_PORT` | `8104` | Port yang di-expose ke luar (host) |
| `CONTAINER_PORT` | `8080` | Port internal container. Default image ini berjalan di port 8080 |

## Secrets (Encrypted)

### Docker Hub Authentication

| Secret | Keterangan |
|--------|------------|
| `DOCKER_HUB_USERNAME` | Username Docker Hub |
| `DOCKER_HUB_TOKEN` | Docker Hub access token (bukan password) |

`Publish Base Image` memakai secrets yang sama. Sebelum deployment pertama, jalankan workflow tersebut secara manual untuk menerbitkan `xyndr0me/pro-bi-smart-base:0.1.0`.

### Google Compute Engine SSH Access

| Secret | Keterangan |
|--------|------------|
| `GCE_HOST` | Alamat IP atau hostname VM, contoh: `203.0.113.50` atau `vm.example.com` |
| `GCE_SSH_USER` | User SSH untuk login, contoh: `ubuntu`, `app`, atau `deploy` |
| `GCE_SSH_PRIVATE_KEY` | Private key SSH (PEM format) dengan newline diganti menjadi `\n` |

## Cara Menghasilkan SSH Private Key

1. Generate key pair (jika belum ada):
   ```bash
   ssh-keygen -t ed25519 -C "deploy@reporaksi-event" -f deploy_key
   ```

2. Tambahkan public key ke VM:
   - Cara 1: Manual --copy isi `deploy_key.pub` ke `~/.ssh/authorized_keys` di VM
   - Cara 2: Gunakan GCE Metadata -> SSH Keys

3. Untuk GitHub secret, format private key:
   ```bash
   # Converts newlines to \n for GitHub secret
   cat deploy_key | awk '{printf "%s\\n", $0}' | head -c -2
   ```
   Atau gunakan raw content dengan editor yang menangani multiline dengan benar.

## VM Prerequisites

Pastikan VM memenuhi requirements:

1. **Docker terinstall** dan user SSH bisa menjalankan `docker`
2. **gcloud CLI terinstall** (bagian dari Google Cloud SDK)
3. **Service Account VM** atau akun yang dipakai `gcloud` di VM memiliki role `roles/secretmanager.secretAccessor` untuk secret `projects/908322652422/secrets/reporaksi-event`
4. **Firewall** mengizinkan akses ke port yang dikonfigurasi (default workflow saat ini: 8104)
5. Jika user SSH bukan root, gunakan `APP_DIR` di dalam home directory user tersebut agar writable
6. Role **Editor** atau akses resource umum di GCP tidak menjamin bisa membaca payload Secret Manager. Permission yang dibutuhkan adalah `secretmanager.versions.access`

## Cara Workflow Mengambil `.env`

Urutan pengambilan secret saat deploy:

1. Coba via **GCE metadata service** menggunakan service account bawaan VM
2. Jika gagal, fallback ke `gcloud secrets versions access`
3. Jika masih gagal tapi file `.env` lama sudah ada di VM, workflow akan tetap deploy menggunakan file tersebut
4. Jika tidak ada akses Secret Manager dan tidak ada file `.env` lama, workflow akan gagal dengan pesan IAM yang lebih jelas

## Cara Konfigurasi

1. Buka **Repository Settings** -> **Secrets and variables** -> **Repository** (atau Environment)
2. Tambahkan setiap secret dengan nilai yang sesuai
3. Untuk Environment, bisa ditambahkan protection rules

## Referensi Workflow

Workflow trigger pada:
- Push ke branch `dev-bedebah`
- Manual trigger melalui **Actions** tab -> **Deploy Reporaksi Event** -> **Run workflow**
