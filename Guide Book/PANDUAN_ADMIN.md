# 🛡️ Panduan Penggunaan — User Admin

Panduan ini ditujukan bagi pengelola katalog dan administrator sistem aplikasi **STEPUP**. Dokumen ini menjelaskan seluruh alur kerja operasional manajemen produk, keamanan data, audit logging, dan alat bantu penunjang.

---

## 1. Kredensial & Autentikasi Admin

Admin memiliki hak akses tertinggi untuk mengelola seluruh katalog sepatu.

* **Email:** `admin@stepup.test`
* **Password:** `password123`
* **Role:** `admin`

### Cara Login ke Akun Admin:
1. Buka browser dan akses **[http://localhost:8000/login](http://localhost:8000/login)** (atau klik menu **Sign In** di navbar).
2. Terdapat 2 opsi login:
   * **Opsi Cepat (1-Click Demo Login):** Klik tombol **"Admin Demo"** di bagian bawah form login. Sistem akan otomatis mengisi form dan membawa Anda langsung masuk.
   * **Opsi Manual:** Masukkan email `admin@stepup.test` dan password `password123`, lalu tekan **Sign In**.
3. Setelah berhasil login, badge indikator status **`● ADMIN`** berwarna hijau akan muncul di navbar bagian atas.

---

## 2. Navigasi Panel Admin (`/admin/products`)

Klik tombol **`● ADMIN`** di navbar atau kunjungi tautan **[http://localhost:8000/admin/products](http://localhost:8000/admin/products)**.

Halaman ini menampilkan tabel ringkasan inventaris produk sepatu dengan fitur:
* **Pencarian Cepat:** Kolom *Search products by name...* untuk menyaring model sepatu secara *real-time*.
* **Filter Kategori:** Dropdown filter (*All Categories*, *Running Shoes*, *Casual Sneakers*, *Formal Shoes*).
* **Pagination:** Navigasi halaman untuk daftar produk yang banyak.
* **Tombol Cepat:**
  * Tombol **"+ Add New Footwear"** untuk mempublikasikan produk baru.
  * Tombol **Edit** di setiap baris untuk mengubah data sepatu.
  * Tombol **Delete** di setiap baris untuk menghapus sepatu.

---

## 3. Menambahkan Produk Sepatu Baru (Create)

1. Dari halaman panel admin, klik tombol hitam **"+ Add New Footwear"** di pojok kanan atas (atau akses `/admin/products/new`).
2. Isi formulir data sepatu:
   * **Model / Style Name (Wajib):** Nama seri sepatu (contoh: `STEPUP Aero Horizon Pro`). Sistem otomatis membuat URL slug yang bersih dan unik (contoh: `stepup-aero-horizon-pro`).
   * **Category (Wajib):** Pilih salah satu kategori yang tersedia (`Running Shoes`, `Casual Sneakers`, atau `Formal Shoes`).
   * **Price (IDR) (Wajib):** Masukkan harga dalam format numerik rupiah (contoh: `850000`).
   * **Editorial Description (Opsional):** Penjelasan material, kegunaan, atau teknologi bantalan sepatu.
   * **Product Image (Opsional / Direkomendasikan):** Klik **"Choose File"** untuk mengunggah foto studio sepatu.
     * Format yang didukung: `.jpg`, `.jpeg`, `.png`, `.webp`.
     * Maksimal ukuran: 5 MB.
     * Pratinjau (*live preview*) foto akan langsung muncul di bawah formulir.
3. Klik tombol **"Publish Footwear"**.
4. Sistem akan menyimpan data ke database MySQL dan memindahkan file foto ke direktori publik `storage/app/public/products`.
5. Notifikasi sukses akan muncul (*"Product created successfully"*) dan Anda diarahkan kembali ke tabel katalog.

---

## 4. Mengubah Data Produk Sepatu (Update)

1. Pada tabel manajemen produk, temukan produk yang ingin diperbarui, lalu klik tombol **"Edit"**.
2. Formulir edit akan terisi otomatis dengan data produk yang ada saat ini beserta foto aktifnya.
3. Anda dapat memperbarui:
   * Nama produk (slug otomatis disesuaikan).
   * Kategori dan harga baru.
   * Deskripsi editorial.
   * **Mengganti Foto:** Unggah foto baru. Sistem secara cerdas akan **menghapus foto lama dari hard disk** dan menggantikannya dengan foto baru untuk menghemat ruang penyimpanan server.
4. Klik tombol **"Save Changes"**.
5. Notifikasi sukses akan muncul dan data di katalog publik langsung terupdate secara instan.

---

## 5. Menghapus Produk Sepatu (Delete)

1. Pada baris produk yang ingin dihapus, klik tombol **"Delete"**.
2. **Modal Konfirmasi Pengamanan:** Sebuah jendela pop-up konfirmasi akan muncul untuk mencegah penghapusan yang tidak disengaja.
3. Klik tombol merah **"Delete Product"** untuk mengonfirmasi.
4. Sistem akan:
   * Menghapus baris produk dari database MySQL.
   * Secara otomatis menghapus file foto produk terkait dari folder `storage/app/public/products` (*zero orphaned files*).
   * Menghapus referensi produk di keranjang belanja user (*cascade delete*).
5. Muncul notifikasi *"Product deleted successfully"*.

---

## 6. Audit Logging & Monitoring Keamanan

Untuk memenuhi standar keamanan dan kepatuhan sistem, seluruh aktivitas manipulasi data oleh Admin dicatat secara permanen di file log server.

### Lokasi File Log:
```text
storage/logs/laravel.log
```

### Contoh Catatan Audit Log:
```log
[2026-10-21 17:15:30] local.INFO: Product created: [ID: 47] 'STEPUP Nova Runner' by Admin [User ID: 1]
[2026-10-21 17:18:42] local.INFO: Product updated: [ID: 47] 'STEPUP Nova Runner V2' by Admin [User ID: 1]
[2026-10-21 17:20:10] local.INFO: Product deleted: [ID: 47] 'STEPUP Nova Runner V2' by Admin [User ID: 1]
```
Admin dan tim IT dapat memantau file log ini untuk kebutuhan investigasi maupun audit operasional.

---

## 7. Menggunakan Swagger UI sebagai Admin

Swagger UI memungkinkan admin atau developer untuk menguji API backend langsung melalui antarmuka web interaktif.

1. Buka **[http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)**.
2. Lakukan login terlebih dahulu melalui endpoint **`POST /api/login`**:
   * Klik tombol **"Try it out"**.
   * Masukkan email `admin@stepup.test` dan password `password123`.
   * Klik **"Execute"**.
   * Salin (*copy*) nilai token yang didapat (misal: `1|eyJ0eXAi...`).
3. Otorisasi Sesi Swagger:
   * Gulir ke bagian paling atas halaman Swagger dan klik tombol hijau **Authorize 🔓**.
   * Pada kolom teks, ketik: `Bearer ` diikuti token Anda (contoh: `Bearer 1|eyJ0eXAi...`).
   * Klik tombol **Authorize**, lalu **Close**.
4. Sekarang Anda dapat mengeksekusi seluruh endpoint yang dilindungi (seperti `POST /api/products`, `PUT /api/products/{id}`, `DELETE /api/products/{id}`) secara langsung dari browser dengan respon status code resmi (`200`, `201`, `204`).

---

## 8. Mengakses Database via phpMyAdmin

Untuk inspeksi data tabular secara langsung:
* **URL:** **[http://localhost:8085](http://localhost:8085)**
* **Server:** `mysql`
* **Username:** `stepup` (atau `root`)
* **Password:** `stepup` (atau `root`)

Tabel utama yang dapat ditinjau:
* **`products`**: Berisi seluruh sepatu, relasi kategori, harga, dan path gambar.
* **`categories`**: Master kategori sepatu.
* **`users`**: Daftar customer dan admin (password di-hash dengan Bcrypt standar industri).
* **`cart_items`**: Data keranjang belanja tersimpan milik user regular.
