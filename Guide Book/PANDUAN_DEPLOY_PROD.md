# 🚀 PANDUAN DEPLOYMENT PRODUKSI — STEPUP
**Panduan Deployment Produksi ke Server SORANTARA (HP 260 G3 Mini / Dokploy / Traefik / Cloudflare Tunnel / HDD Backup)**

Dokumen ini disusun khusus untuk deployment aplikasi **STEPUP** ke lingkungan server produksi Ubuntu Server 24.04 LTS yang dikelola menggunakan **Dokploy**, reverse proxy **Traefik**, **Cloudflare Tunnel**, dan penyimpanan cadangan sekunder di `/srv/backup` (HDD 500GB).

---

## 🏗️ 1. Arsitektur Lingkungan Produksi

Arsitektur produksi STEPUP terisolasi secara aman menggunakan Docker container dan bridge network, sekaligus terintegrasi dengan Traefik melalui `dokploy-network`:

```text
                           INTERNET (User)
                                 │
                                 │ HTTPS (Port 443)
                                 ▼
                     ┌───────────────────────┐
                     │   CLOUDFLARE TUNNEL   │
                     │  (stepup.sorantara.id) │
                     └───────────┬───────────┘
                                 │ Encrypted Tunnel
                                 ▼
                     ┌───────────────────────┐
                     │    cloudflared svc    │
                     └───────────┬───────────┘
                                 │ HTTPS
                                 ▼
                     ┌───────────────────────┐
                     │      TRAEFIK v3       │
                     │   (dokploy-network)   │
                     └───────────┬───────────┘
                                 │
                 ┌───────────────┴───────────────┐
                 │                               │
                 ▼                               ▼
       sorantara-profile                      STEPUP
     (Port 8085 / Nginx)              (Port 8088 / Nginx)
                                                 │
                               ┌─────────────────┼─────────────────┐
                               ▼                 ▼                 ▼
                        stepup-prod-web   stepup-prod-app   stepup-prod-mysql
                         (Nginx 1.25)     (PHP-FPM 8.3)        (MySQL 8.0)
                                                 │                 │
                                         ┌───────┴───────┐         │
                                         ▼               ▼         ▼
                                    Queue Worker   Task Scheduler Data Volume
```

### Rincian Layanan & Kontainer:
| Kontainer | Image / Base | Peran & Konfigurasi Utama |
|---|---|---|
| `stepup-prod-web` | `nginx:1.25-alpine` | Web server & reverse proxy FastCGI, rate limiting, gzip, Vite static asset cache 1 tahun, listen port 80 & LAN port 8088. Terhubung ke `dokploy-network`. |
| `stepup-prod-app` | `php:8.3-fpm-alpine` | Core aplikasi Laravel, OPcache bytecode (`opcache.validate_timestamps=0`), entrypoint otomatis migrasi & cache. |
| `stepup-prod-mysql` | `mysql:8.0` | Database relasional MySQL dengan persistent volume `stepup_mysql_data`. |
| `stepup-prod-queue` | `php:8.3-fpm-alpine` | Worker antrean background (`queue:work`) untuk pesanan, sinkronisasi, dan email. |
| `stepup-prod-cron` | `php:8.3-fpm-alpine` | Task scheduler Laravel (`schedule:work`) untuk tugas berkala. |

---

## 📦 2. Komponen File Konfigurasi Produksi

Seluruh file konfigurasi produksi telah tersedia di root repositori:

1. **`Dockerfile.production`**: Multi-stage build Node.js 20 (Vite build) + PHP 8.3 FPM Alpine dengan OPcache dan resource limits produksi.
2. **`docker/nginx/Dockerfile.production`**: Nginx image mandiri dengan file build frontend dan konfigurasi proxy.
3. **`docker/nginx/default.conf`**: Nginx virtual host dengan security headers, Gzip compression, SPA fallback, dan FastCGI buffer tuning.
4. **`docker-entrypoint.prod.sh`**: Skrip startup otomatis untuk migrasi database, storage symlink, OpenAPI generation, dan `config/route/view:cache`.
5. **`docker-compose.prod.yml`**: Compose stack untuk Dokploy/Docker Swarm lengkap dengan Traefik routing labels.
6. **`.env.production.example`**: Template environment variabel produksi.
7. **`scripts/backup-stepup.sh`**: Skrip backup otomatis logical dump MySQL + storage tarball ke `/srv/backup/stepup/`.
8. **`scripts/stepup-backup.service` & `scripts/stepup-backup.timer`**: Unit systemd untuk otomasi backup harian pukul 03:30 WIB.

---

## 🛠️ 3. Langkah Demi Langkah Deploy via Dokploy

### Tahap 1: Akses Dashboard Dokploy
Masuk ke Dokploy via Tailscale:
```text
http://<IP-SERVER-TAILSCALE>:3000
```

### Tahap 2: Buat Compose Application di Dokploy
1. Masuk ke menu **Projects** > Pilih atau buat project `stepup`.
2. Klik **Create Service** > Pilih opsi **Compose**.
3. Isi konfigurasi:
   - **Service Name:** `stepup-storefront`
   - **Repository:** Hubungkan ke repository GitHub Anda.
   - **Branch:** `main`
   - **Compose Path:** `docker-compose.prod.yml`
4. Aktifkan **Auto Deploy (Webhook)** agar setiap push commit ke branch `main` langsung dideploy secara otomatis.

### Tahap 3: Set Environment Variables
Buka tab **Environment** pada service `stepup-storefront`, lalu masukkan variabel produksi (sesuaikan dengan kredensial rahasia Anda):

```ini
APP_NAME="STEPUP"
APP_ENV=production
APP_KEY=base64:3wa9nWJ2F67Uy0NUjEEOKNe1wK6WYz7/yW1wXuUvg9o=
APP_DEBUG=false
APP_URL=https://stepup.sorantara.id
APP_DOMAIN=stepup.sorantara.id
STEPUP_WEB_PORT=8088

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=stepup_db
DB_USERNAME=stepup_user
DB_PASSWORD=YOUR_STRONG_PASSWORD_HERE
DB_ROOT_PASSWORD=YOUR_STRONG_ROOT_PASSWORD_HERE

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### Tahap 4: Klik Deploy
Klik **Deploy**. Dokploy akan meng-clone source code, melakukan build multi-stage, membuat volume, dan menyalakan kontainer.

---

## 🌐 4. Konfigurasi Routing Cloudflare Tunnel & Traefik

Aplikasi dialihkan ke internet publik melalui Cloudflare Tunnel tanpa perlu membuka port router/IP publik:

1. Buka [Cloudflare Zero Trust Dashboard](https://one.dash.cloudflare.com/) > **Networks** > **Tunnels**.
2. Pilih tunnel yang berjalan di server (`sorantara-production`).
3. Tambahkan **Public Hostname**:
   - **Subdomain:** `stepup`
   - **Domain:** `sorantara.id` (atau domain Anda)
   - **Type:** `HTTPS`
   - **URL:** `localhost:443`
   - **TLS Settings:** Aktifkan toggle `No TLS Verify: true` (agar validasi sertifikat lokal Traefik lolos tanpa kendala).
4. Klik **Save Hostname**.

Traffic publik akan dialirkan dari Cloudflare Edge → `cloudflared` → Traefik (`dokploy-network`) → `stepup-prod-web`.

---

## 💾 5. Inisialisasi Database Pertama Kali (Seeder & Storage)

Setelah kontainer produksi pertama kali berjalan:

```bash
# 1. Jalankan seeder database (Katalog Sepatu, Kategori, User Default)
docker exec -it stepup-prod-app php artisan db:seed --force

# 2. Hubungkan symlink storage
docker exec -it stepup-prod-app php artisan storage:link

# 3. Generate OpenAPI / Swagger
docker exec -it stepup-prod-app php artisan l5-swagger:generate

# 4. Optimalkan cache Laravel
docker exec -it stepup-prod-app php artisan config:cache
docker exec -it stepup-prod-app php artisan route:cache
docker exec -it stepup-prod-app php artisan view:cache
```

Akun bawaan sistem:
* **Admin:** `admin@stepup.test` (Password: `password123`)
* **User Regular:** `user@stepup.test` (Password: `password123`)

---

## 🛡️ 6. Setup Backup Otomatis Harian ke HDD 500GB (`/srv/backup`)

Sesuai filosofi reliability server SORANTARA:
> *"Deploy adalah kemampuan menjalankan aplikasi. Backup adalah kemampuan menyelamatkan data. Restore adalah kemampuan membuktikan backup benar-benar berguna."*

Pasang backup harian otomatis ke secondary HDD `/srv/backup/stepup`:

```bash
# 1. Buat direktori backup di secondary HDD
sudo mkdir -p /srv/backup/stepup

# 2. Salin dan beri izin eksekusi skrip backup
sudo cp /path/to/stepup/scripts/backup-stepup.sh /usr/local/sbin/backup-stepup.sh
sudo chmod +x /usr/local/sbin/backup-stepup.sh

# 3. Pasang Systemd Service dan Timer
sudo cp /path/to/stepup/scripts/stepup-backup.service /etc/systemd/system/
sudo cp /path/to/stepup/scripts/stepup-backup.timer /etc/systemd/system/

# 4. Aktifkan timer systemd
sudo systemctl daemon-reload
sudo systemctl enable --now stepup-backup.timer

# 5. Uji eksekusi langsung sekali
sudo /usr/local/sbin/backup-stepup.sh

# 6. Verifikasi file hasil backup dan log
ls -lh /srv/backup/stepup
cat /srv/backup/stepup/backup.log
```

Skrip ini otomatis membersihkan file backup yang berusia lebih dari 7 hari (`retention policy: 7 days`).

---

## 📊 7. Monitoring & Healthcheck Perintah Diagnostik

```bash
# Cek semua kontainer stepup yang sedang aktif
docker ps --filter "name=stepup-prod-"

# Cek log aplikasi utama
docker logs -f --tail=100 stepup-prod-app

# Cek log Nginx
docker logs -f --tail=100 stepup-prod-web

# Cek log queue worker
docker logs -f --tail=100 stepup-prod-queue

# Uji koneksi lokal langsung di server (Port 8088)
curl -I http://localhost:8088

# Cek status timer backup harian
systemctl list-timers | grep stepup
```

---

## 👟 8. Favicon & Identitas Brand

Favicon STEPUP telah terdaftar dan siap disajikan di produksi:
- Vector Modern: `/favicon.svg`
- Binary Multi-Res: `/favicon.ico`
- Web 32x32: `/favicon-32x32.png`
- iOS / Mobile Touch: `/apple-touch-icon.png`
- Theme Color: `#09090b`
