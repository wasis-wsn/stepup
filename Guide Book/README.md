# 📚 STEPUP — Master Guide Book

Selamat datang di pusat dokumentasi dan panduan penggunaan aplikasi **STEPUP (Footwear Catalog & E-Commerce Platform)**.

Dokumentasi ini disusun secara terperinci untuk memandu seluruh pengguna sistem, mulai dari **User Regular**, **User Admin**, hingga **Software Engineer / Reviewer**.

---

## 🗂️ Daftar Panduan

Silakan pilih panduan sesuai dengan peran dan kebutuhan Anda:

| Dokumen | Target Pengguna | Deskripsi Isi |
|---|---|---|
| **[1. Panduan Admin](PANDUAN_ADMIN.md)** | **Admin Store Manager** | Tata cara login admin, manajemen produk sepatu (Create, Read, Update, Delete), upload foto produk, monitoring log audit, phpMyAdmin, serta penggunaan Swagger untuk admin. |
| **[2. Panduan User Regular & Guest](PANDUAN_USER_REGULAR.md)** | **Customer & Pengunjung** | Tata cara menjelajahi katalog, pencarian & filter sepatu, pemilihan ukuran sepatu (EU 39–45), belanja sebagai tamu tanpa paksaan login, pendaftaran akun, hingga sinkronisasi otomatis keranjang belanja ke database MySQL. |
| **[3. Panduan Teknis & Pengembang](PANDUAN_TEKNIS_DEVELOPER.md)** | **Software Engineer & Reviewer** | Arsitektur sistem (Laravel 11 + Vue 3), setup Docker Compose, eksekusi automated test PHPUnit (41 tests), Swagger OpenAPI 3.0, optimasi OPcache, dan cheatsheet REST API. |
| **[4. Panduan Deployment Produksi](PANDUAN_DEPLOY_PROD.md)** | **DevOps & Server Admin** | Panduan lengkap deploy produksi ke server SORANTARA (HP 260 G3 Mini, Dokploy, Traefik, Cloudflare Tunnel, dan backup otomatis ke HDD 500GB `/srv/backup`). |

---

## 🔑 Akun & Kredensial Bawaan (Default Credentials)

Untuk mempermudah peninjauan (*review*) dan pengujian sistem, akun bawaan berikut telah disediakan di database:

### 1. Akun Admin
* **Email:** `admin@stepup.test`
* **Password:** `password123`
* **Hak Akses:** Penuh (Katalog, Tambah Sepatu, Edit Sepatu, Hapus Sepatu, Swagger API)

### 2. Akun User Regular
* **Email:** `user@stepup.test`
* **Password:** `password123`
* **Hak Akses:** Belanja publik, Keranjang Belanja Database (`cart_items`), Profil Akun

### 3. Akses Database phpMyAdmin
* **URL:** `http://localhost:8085`
* **Server:** `mysql`
* **Username:** `stepup` (atau `root`)
* **Password:** `stepup` (atau `root`)

---

## 🌐 Tautan Layanan Utama

* **Aplikasi Web (Storefront & Admin):** [http://localhost:8000](http://localhost:8000)
* **Dokumentasi API Interaktif (Swagger UI):** [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)
* **Database Manager (phpMyAdmin):** [http://localhost:8085](http://localhost:8085)
