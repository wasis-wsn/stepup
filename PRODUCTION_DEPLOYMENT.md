# STEPUP — Production Deployment Guide
Dokumentasi Deployment Produksi ke Server SORANTARA (HP 260 G3 Mini / Dokploy / Traefik / Cloudflare Tunnel)

---

## 1. Arsitektur Produksi

Aplikasi **STEPUP** dirancang agar selaras dengan arsitektur server produksi SORANTARA sebagaimana tercantum pada `SORANTARA-SERVER-SETUP.md`:

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
     (Port 8085 / Nginx)              (Port 8086 / Nginx)
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

### Karakteristik & Isolasi:
1. **Web Server (`stepup-prod-web`):** Nginx 1.25 Alpine yang mandiri mengompilasi asset frontend (Vite) dan menyajikan file statis dengan HTTP caching 1 tahun (`Cache-Control: immutable`), kompresi Gzip, rate-limiting, dan meneruskan request dinamis ke PHP-FPM.
2. **PHP-FPM (`stepup-prod-app`):** PHP 8.3 Alpine dengan OPcache agresif (`opcache.validate_timestamps=0`) untuk latensi sub-milidetik.
3. **Port Local/LAN:** Dialokasikan port `8086` (sehingga tidak bentrok dengan `sorantara-profile` di port `8085`).
4. **Dokploy/Traefik Ingress:** Kontainer `stepup-prod-web` terhubung ke `dokploy-network` dengan label Traefik untuk routing domain otomatis.
5. **Database Terisolasi (`stepup-prod-mysql`):** MySQL 8.0 dengan persistent volume `stepup_mysql_data`.
6. **Background Services:** `stepup-prod-queue` (pemrosesan pesanan/email) & `stepup-prod-cron` (jadwal tugas berkala).
7. **Penyimpanan Backup:** Terintegrasi langsung dengan secondary storage HDD 500GB di `/srv/backup/stepup/`.

---

## 2. Persiapan File di Server

Seluruh file konfigurasi produksi telah disediakan di repositori ini:
- `Dockerfile.production` (PHP-FPM 8.3 + Node builder)
- `docker/nginx/Dockerfile.production` & `docker/nginx/default.conf` (Nginx SPA proxy)
- `docker-entrypoint.prod.sh` (Auto migration, symlink, caching)
- `docker-compose.prod.yml` (Dokploy/Swarm production stack)
- `.env.production.example` (Template environment produksi)
- `scripts/backup-stepup.sh` (Skrip backup database & storage otomatis)
- `scripts/stepup-backup.service` & `scripts/stepup-backup.timer` (Systemd timer backup harian)

---

## 3. Langkah Deployment via Dokploy

Dokploy dapat diakses melalui Tailscale di `http://<SERVER-IP-TAILSCALE>:3000`.

### Langkah 3.1: Buat Project & Compose Stack di Dokploy
1. Buka dashboard Dokploy.
2. Buat **Project** baru bernama `stepup` (atau masukkan ke project yang sudah ada).
3. Klik **Create Service** > Pilih **Compose**.
4. Beri nama service: `stepup-storefront`.
5. Pada tab **Source**:
   - Pilih **Git** (arahkan ke repositori GitHub Anda dan branch `main`).
   - Tentukan Compose Path: `docker-compose.prod.yml`.
6. Aktifkan fitur **Auto Deploy** (GitHub Webhook) agar setiap kali ada `git push`, Dokploy otomatis melakukan build dan reload tanpa downtime.

### Langkah 3.2: Isi Environment Variables
Pada tab **Environment** di Dokploy, salin isi `.env.production.example` dan sesuaikan nilainya:

```ini
APP_NAME="STEPUP"
APP_ENV=production
APP_KEY=base64:3wa9nWJ2F67Uy0NUjEEOKNe1wK6WYz7/yW1wXuUvg9o=
APP_DEBUG=false
APP_URL=https://stepup.sorantara.id
APP_DOMAIN=stepup.sorantara.id
STEPUP_WEB_PORT=8086

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=stepup_db
DB_USERNAME=stepup_user
DB_PASSWORD=GantiDenganPasswordKuat123!
DB_ROOT_PASSWORD=GantiDenganRootPasswordKuat123!

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

> **Tips:** Anda dapat membuat APP_KEY baru yang aman dengan menjalankan: `php artisan key:generate --show`.

### Langkah 3.3: Deploy Stack
Klik tombol **Deploy** di Dokploy. Dokploy akan:
1. Mem-pull source code dari Git.
2. Mengompilasi asset React/Vite di Node container.
3. Membangun container `stepup-prod-app` dan `stepup-prod-web`.
4. Menghubungkan container ke network internal `stepup-network` dan Traefik `dokploy-network`.
5. Menjalankan migrasi database otomatis melalui `docker-entrypoint.prod.sh`.

---

## 4. Konfigurasi Cloudflare Tunnel & Domain Ingress

Agar domain `stepup.sorantara.id` (atau domain Anda) dapat diakses publik dengan aman:

1. Buka **Cloudflare Zero Trust Dashboard** > **Networks** > **Tunnels**.
2. Pilih tunnel server Anda (`sorantara-production`).
3. Tambahkan **Public Hostname**:
   - **Subdomain:** `stepup`
   - **Domain:** `sorantara.id` (atau domain aktif Anda)
   - **Service Type:** `HTTPS`
   - **URL:** `localhost:443`
   - **Additional application settings:** Buka tab *TLS* > Aktifkan `No TLS Verify: true` (karena request diteruskan ke Traefik port 443 lokal).
4. Klik **Save hostname**.

Traefik pada `dokploy-network` akan secara otomatis mencocokkan header Host `stepup.sorantara.id` dan mengarahkannya ke `stepup-prod-web:80`.

---

## 5. Inisialisasi Data Pertama Kali (Seeder & Storage)

Setelah aplikasi pertama kali berjalan di server, isi data master kategori, katalog sepatu, dan user default (Admin & Regular User):

Masuk ke terminal server (via SSH / Tailscale) dan jalankan:

```bash
# 1. Jalankan seeder database
docker exec -it stepup-prod-app php artisan db:seed --force

# 2. Pastikan symlink storage terhubung
docker exec -it stepup-prod-app php artisan storage:link

# 3. Generate OpenAPI / Swagger API Docs
docker exec -it stepup-prod-app php artisan l5-swagger:generate

# 4. Optimalkan cache Laravel untuk performa maksimal
docker exec -it stepup-prod-app php artisan config:cache
docker exec -it stepup-prod-app php artisan route:cache
docker exec -it stepup-prod-app php artisan view:cache
```

Akun bawaan setelah seeder:
- **Admin:** `admin@stepup.com` (Password: `password123`)
- **Regular User:** `user@stepup.com` (Password: `password123`)

---

## 6. Pasang Otomasi Backup Harian ke `/srv/backup/stepup`

Sesuai standar server SORANTARA pada Bab 4 dan Bab 25:
Data database dan folder storage harus dibackup harian ke HDD 500GB di `/srv/backup`.

Jalankan perintah berikut di server Ubuntu:

```bash
# 1. Buat folder backup khusus stepup di secondary HDD
sudo mkdir -p /srv/backup/stepup

# 2. Salin skrip backup ke direktori sistem
sudo cp /path/to/stepup/scripts/backup-stepup.sh /usr/local/sbin/backup-stepup.sh
sudo chmod +x /usr/local/sbin/backup-stepup.sh

# 3. Salin service dan timer systemd
sudo cp /path/to/stepup/scripts/stepup-backup.service /etc/systemd/system/
sudo cp /path/to/stepup/scripts/stepup-backup.timer /etc/systemd/system/

# 4. Reload daemon dan aktifkan timer harian (03:30 WIB)
sudo systemctl daemon-reload
sudo systemctl enable --now stepup-backup.timer

# 5. Uji jalankan backup pertama kali
sudo /usr/local/sbin/backup-stepup.sh

# 6. Cek hasil backup
ls -lh /srv/backup/stepup
cat /srv/backup/stepup/backup.log
```

---

## 7. Pemantauan & Perintah Diagnostik

```bash
# Cek seluruh kontainer STEPUP
docker ps --filter "name=stepup-prod-"

# Cek log aplikasi Laravel (PHP-FPM)
docker logs -f --tail=100 stepup-prod-app

# Cek log web server Nginx
docker logs -f --tail=100 stepup-prod-web

# Cek log antrean worker
docker logs -f --tail=100 stepup-prod-queue

# Uji akses lokal langsung (Port 8086)
curl -I http://localhost:8086

# Uji status timer backup
systemctl list-timers | grep stepup
```

---

## 8. Verifikasi Favicon & Asset Statis

Favicon modern minimalist STEPUP telah tersedia dalam format vector dan binary:
- `https://stepup.sorantara.id/favicon.svg` (Scalable vector logo)
- `https://stepup.sorantara.id/favicon.ico` (Legacy & multi-resolution icon)
- `https://stepup.sorantara.id/favicon-32x32.png` (Standard web icon)
- `https://stepup.sorantara.id/apple-touch-icon.png` (Mobile & iOS touch icon)
