# 🛍️ Panduan Penggunaan — User Regular & Guest

Panduan ini ditujukan bagi pelanggan dan pengunjung situs **STEPUP**. Dokumen ini menjelaskan cara menjelajahi katalog sepatu, melakukan pencarian dan penyaringan, memilih ukuran sepatu, hingga memanfaatkan fitur keranjang belanja tersimpan di database.

---

## 1. Peran Pengguna: Guest vs. Regular User

STEPUP memberikan pengalaman berbelanja yang nyaman dan tanpa hambatan (*frictionless*):

| Kategori Pengguna | Status Login | Penyimpanan Keranjang Belanja | Fitur Tambahan |
|---|---|---|---|
| **Guest (Tamu)** | Belum Login | Disimpan lokal di browser (`localStorage`) | Bebas menjelajahi seluruh katalog, filter harga, memilih ukuran sepatu, dan memasukkan barang ke keranjang **tanpa dipaksa registrasi**. |
| **Regular User** | Sudah Login | **Tersimpan persisten di Database MySQL** (`cart_items`) | Barang belanjaan tersimpan permanen di akun database, sinkronisasi otomatis antar-perangkat, dan siap untuk checkout. |

---

## 2. Menjelajahi Toko (Home & Katalog Sepatu)

### A. Halaman Utama (Editorial Home)
Akses alamat: **[http://localhost:8000](http://localhost:8000)**.
* Menampilkan *Hero Showcase* kontemporer bertema *"Every step tells a story"*.
* Pratinjau koleksi unggulan (*Curated Releases*).
* Navigasi pintas ke kategori populer: **Running Shoes**, **Casual Sneakers**, dan **Formal Shoes**.

### B. Halaman Koleksi Penuh ("The Collection")
Akses alamat: **[http://localhost:8000/products](http://localhost:8000/products)** (atau klik menu **"Shop"** di navbar).

Di halaman katalog, Anda dapat menggunakan alat bantu penyaringan:
1. **Pencarian Nama Produk:** Ketikkan nama atau model sepatu di kolom pencarian. Hasil akan difilter secara otomatis tanpa perlu me-reload halaman.
2. **Filter Kategori Cepat:** Klik tab pill kategori di bagian atas:
   * **All Styles** (semua sepatu)
   * **Running Shoes** (sepatu lari & olahraga)
   * **Casual Sneakers** (sneakers santai gaya hidup)
   * **Formal Shoes** (pantofel, loafer, dan oxford resmi)
3. **Filter Rentang Harga:** 
   * Pilih preset: *Under IDR 500K*, *IDR 500K - 800K*, atau *Above IDR 800K*.
   * Atau masukkan harga minimum dan maksimum kustom Anda sendiri.
4. **Pengurutan (Sort By):**
   * *Newest Arrivals* (terbaru)
   * *Price: Low to High* (harga termurah ke termahal)
   * *Price: High to Low* (harga termahal ke termurah)

---

## 3. Melihat Detail & Memilih Ukuran Sepatu

1. Klik pada kartu produk sepatu mana pun untuk membuka halaman detail sepatu (contoh: `/products/44`).
2. Di halaman detail, Anda disajikan:
   * Fotografi studio sepatu beresolusi tinggi.
   * Informasi harga resmi dalam format Rupiah (contoh: `IDR 750.000`).
   * Deskripsi material, fungsi performa, dan karakteristik sol sepatu.
   * **Pemilih Ukuran Sepatu (Size Selector):**
     * Tersedia pilihan ukuran standar Eropa: **EU 39**, **40**, **41**, **42**, **43**, **44**, dan **45**.
     * Klik pada angka ukuran yang Anda inginkan (ukuran terpilih akan disorot dengan warna hitam kontras).
3. Klik tombol hitam besar **"Add to Bag"**.
4. Item akan langsung meluncur masuk ke dalam keranjang belanja dan laci keranjang (*Cart Drawer*) akan terbuka secara otomatis.

---

## 4. Pengalaman Tamu (Guest Shopping)

* Sebagai tamu baru yang belum memiliki akun, Anda **tidak perlu terburu-buru mendaftar**.
* Anda dapat menambahkan 1, 2, atau beberapa pasang sepatu dengan ukuran berbeda ke dalam keranjang belanja.
* Keranjang belanja tamu disimpan secara aman di peramban Anda. Anda bebas menutup peramban dan membukanya kembali nanti tanpa khawatir barang belanjaan Anda hilang.

---

## 5. Pendaftaran Akun Baru & Login Customer

Ketika Anda memutuskan untuk menyimpan barang belanjaan secara permanen di database akun Anda:

### A. Mendaftar Akun Baru (`/register`)
1. Klik menu **Sign In** di navbar, lalu pilih tautan **"Create Account"** (atau kunjungi `/register`).
2. Masukkan nama lengkap, alamat email, dan buat password minimal 8 karakter.
3. Konfirmasi password Anda dan klik **"Create Account"**.
4. Akun Anda otomatis dibuat dengan status hak akses `user` yang aman.

### B. Login ke Akun Regular (`/login`)
1. Akses **[http://localhost:8000/login](http://localhost:8000/login)**.
2. Anda dapat menggunakan akun demo yang telah disediakan:
   * **Opsi Cepat:** Klik tombol **"Customer Demo"** untuk pengisian instan.
   * **Opsi Manual:** Email `user@stepup.test` dan password `password123`.
3. Klik **Sign In**.

---

## 6. Sinkronisasi Otomatis ke Database MySQL

Ini adalah fitur utama yang dirancang untuk kenyamanan customer:

1. Jika sebelum login Anda telah memilih beberapa sepatu di keranjang sebagai tamu...
2. Begitu Anda berhasil **Sign In** sebagai Regular User:
   * Sistem front-end secara cerdas memanggil API sinkronisasi: `POST /api/cart/sync`.
   * Seluruh barang yang ada di browser Anda otomatis dipindahkan dan **disimpan ke database MySQL server** di tabel `cart_items`.
   * Tidak ada satu pun barang belanjaan yang hilang atau tereset!
3. Jika di kemudian hari Anda login dari komputer lain, laptop kerja, atau smartphone, seluruh barang di keranjang belanja Anda akan tetap ada karena tersimpan di cloud database kami.

---

## 7. Mengelola Keranjang Belanja (*Cart Drawer*)

Untuk melihat atau mengubah pesanan, klik ikon **Tas Belanja (Shopping Bag)** di navbar kanan atas:

1. **Ubah Jumlah Barang (+ / -):**
   * Gunakan tombol minus (`-`) atau plus (`+`) pada setiap produk untuk menaikkan atau menurunkan jumlah pasang sepatu.
   * Jumlah diperbarui secara *real-time* ke database MySQL.
2. **Hapus Barang:**
   * Klik ikon tempat sampah kecil di samping produk untuk membatalkan pilihan sepatu tersebut.
3. **Indikator Bebas Ongkir (Free Shipping Indicator):**
   * Keranjang belanja memiliki batas gratis ongkir cerdas (otomatis aktif untuk pesanan di atas IDR 1.000.000).
   * Status kalkulasi ongkos kirim ditampilkan transparan pada subtotal belanja.
4. **Proses Checkout:**
   * Klik tombol hitam **"Proceed to Checkout"**.
   * Jendela konfirmasi rincian pembayaran akan tampil untuk mereview total belanja sebelum memproses pesanan.

---

## 8. Perlindungan Akses (*Role-Based Security*)

Sebagai Regular User, data Anda terlindungi:
* Anda tidak dapat mengakses atau memodifikasi katalog admin (`/admin/products`).
* Jika ada upaya membuka URL admin secara paksa, sistem keamanan kami akan langsung mengembalikan respon penolakan **`403 Forbidden`** demi menjaga integritas data katalog toko.
