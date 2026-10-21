# STEPUP — Footwear Catalog Dataset & Assets

Direktori ini (`data_shoes/`) berisi kumpulan dataset produk sepatu, metadata katalog, dan gambar produk beresolusi tinggi (commercial studio photography) yang siap digunakan untuk demo, testing, atau import database pada aplikasi **STEPUP**.

---

## 📁 Struktur Direktori

```text
data_shoes/
├── images/
│   ├── aerorun-white.jpg          # Running Shoes: Modern Minimalist White Mesh Runner (1:1)
│   ├── urban-black-gum.jpg        # Casual Sneakers: Matte Black Suede & Gum Sole (1:1)
│   ├── retro-vintage-low.jpg      # Casual Sneakers: 90s Heritage Court Sneaker in Off-White & Sage (1:1)
│   ├── oxford-monarch-cognac.jpg  # Formal Shoes: Hand-burnished Cognac Calfskin Oxford (1:1)
│   ├── trail-strider-green.jpg    # Running Shoes: All-weather Lugged Trail Runner (1:1)
│   └── loafer-prestige-black.jpg  # Formal Shoes: Smooth Black Box Calf Penny Loafer (1:1)
├── shoes.json                     # Full dataset dalam format JSON (dengan tags, color, material)
├── shoes.csv                      # Dataset format CSV untuk Excel / Spreadsheet / phpMyAdmin
├── shoes.sql                      # SQL INSERT query untuk import langsung ke tabel MySQL `products`
└── README.md                      # Panduan dokumentasi dataset ini
```

---

## 👟 Katalog Produk yang Tersedia

| ID | Nama Produk | Kategori | Harga (IDR) | Warna / Material | File Gambar |
|---|---|---|---|---|---|
| **1** | STEPUP AeroRun Pro | Running Shoes | Rp 450.000 | Triple White (Mesh Knit / EVA) | `aerorun-white.jpg` |
| **2** | STEPUP Urban Classic | Casual Sneakers | Rp 350.000 | Matte Black / Gum Sole | `urban-black-gum.jpg` |
| **3** | STEPUP Velocity Blast | Running Shoes | Rp 699.000 | Ice White / Platinum Carbon Plate | `aerorun-white.jpg` |
| **4** | STEPUP Street Glide | Casual Sneakers | Rp 389.000 | Onyx Black Suede | `urban-black-gum.jpg` |
| **5** | STEPUP Oxford Monarch | Formal Shoes | Rp 850.000 | Burnished Cognac Calfskin | `oxford-monarch-cognac.jpg` |
| **6** | STEPUP Derby Executive | Formal Shoes | Rp 920.000 | Rich Chestnut Leather | `oxford-monarch-cognac.jpg` |
| **7** | STEPUP CloudPacer | Running Shoes | Rp 540.000 | Cloud White Dual-Density | `aerorun-white.jpg` |
| **8** | STEPUP Retro Low Vintage | Casual Sneakers | Rp 420.000 | Off-White & Sage Green Suede | `retro-vintage-low.jpg` |
| **9** | STEPUP Trail Strider | Running Shoes | Rp 580.000 | Olive Green All-Weather Ripstop | `trail-strider-green.jpg` |
| **10** | STEPUP Canvas Breeze | Casual Sneakers | Rp 275.000 | Chalk White & Vintage Green | `retro-vintage-low.jpg` |
| **11** | STEPUP Loafer Prestige | Formal Shoes | Rp 780.000 | Classic Deep Black Box Calf | `loafer-prestige-black.jpg` |
| **12** | STEPUP Sprint Elite | Running Shoes | Rp 750.000 | Pure White Racing Flat | `aerorun-white.jpg` |

---

## 🚀 Cara Menggunakan Data Ini

### 1. Digunakan Otomatis oleh Laravel Seeder
Semua gambar pada `data_shoes/images/` telah disinkronkan ke direktori `storage/app/public/products/` dan `public/storage/products/`.
Anda dapat menjalankan ulang seeder dengan:
```bash
php artisan db:seed --class=ProductSeeder
```
atau di dalam Docker:
```bash
docker compose exec app php artisan db:seed --class=ProductSeeder
```

### 2. Import Langsung Lewat phpMyAdmin
1. Buka phpMyAdmin di [http://localhost:8085](http://localhost:8085)
2. Pilih database `stepup`
3. Klik menu **Import** atau **SQL**
4. Salin isi file `data_shoes/shoes.sql` atau unggah file `data_shoes/shoes.csv`

### 3. Digunakan untuk Pengujian REST API / Form Upload
Gunakan file gambar di `data_shoes/images/` sebagai sampel file gambar saat menguji endpoint admin:
- `POST /api/products` (Create Product)
- `POST /api/products/{id}` (Update Product)
