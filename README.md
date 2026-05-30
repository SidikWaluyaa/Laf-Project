# 🚀 Smart POS with FP-Growth Recommendation Engine (Laf Project)

[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)](https://alpinejs.dev)

---

## 📌 Executive Summary
**Laf Project Smart POS** adalah platform *Point of Sales* (POS) dan Manajemen Inventaris berbasis web yang dirancang secara khusus untuk ritel modern. Berbeda dengan aplikasi kasir konvensional yang hanya berfungsi sebagai pencatat transaksi pasif, sistem ini diinjeksikan dengan **Data Mining Engine** menggunakan **Algoritma FP-Growth (Frequent Pattern Growth)**. 

Melalui teknik *Market Basket Analysis*, sistem ini mampu menganalisis ribuan data transaksi penjualan secara real-time untuk menemukan pola tersembunyi perilaku belanja konsumen. Output utama dari sistem ini adalah **Rekomendasi Paket Promo Produk (Bundling)** yang akurat dan berbasis data (*data-driven*), membantu pemilik bisnis meningkatkan efisiensi pemasaran dan mengoptimalkan perputaran stok barang.

---

## 💡 Product Requirement Document (PRD)

---

### 1. Analisis Masalah Mendalam (The Problem Statement)

Dalam industri ritel dan *fashion* seperti **Laf Project**, manajemen operasional sering menghadapi tantangan klasik yang menghambat efisiensi bisnis:

```
┌────────────────────────────────────────────────────────┐
│                   FENOMENA MASALAH                     │
└───────────────────────────┬────────────────────────────┘
                            │
            ┌───────────────┴───────────────┐
            ▼                               ▼
  [ kaya data, miskin informasi ]  [ pengambilan keputusan subjektif ]
            │                               │
            ▼                               ▼
  Tumpukan data transaksi kasir    Penentuan paket promo hanya
  hanya berakhir sebagai struk     berdasarkan intuisi / perasaan
  dan arsip laporan keuangan.      (trial & error) tanpa data valid.
```

*   **1. Fenomena "Sampah Data" (Data Rich, Information Poor)**
    Setiap transaksi di kasir menghasilkan data detail berupa item yang dibeli, kuantitas, harga, dan waktu. Namun, data transaksi historis ini hanya dibiarkan menumpuk di database sebagai arsip digital dan laporan keuangan semata. Data ini tidak diekstraksi maknanya untuk perencanaan strategis penjualan berikutnya.
*   **2. Subjektivitas Tinggi dalam Keputusan Bisnis**
    Selama ini, divisi pemasaran Laf Project menentukan strategi promo (seperti bundling produk atau paket diskon) secara manual berdasarkan *intuisi*, *perasaan*, atau *tren sekilas*. Hal ini menimbulkan risiko tinggi karena kombinasi paket produk seringkali tidak sejalan dengan preferensi riil konsumen di lapangan.
*   **3. Risiko Finansial "Trial and Error"**
    Meluncurkan paket promo tanpa dasar ilmiah sering berujung kegagalan. Biaya operasional (seperti cetak banner, pembuatan promosi digital, dan pemotongan margin harga) terbuang sia-sia apabila paket promo tersebut tidak diminati oleh pelanggan.
*   **4. Penumpukan Stok Pasif (Dead Stock)**
    Produk dengan tingkat penjualan lambat (*slow-moving*) seringkali mengendap di gudang dalam waktu lama. Tanpa analisis keranjang belanja yang cerdas, manajemen kesulitan menemukan produk terlaris (*fast-moving*) mana yang secara alamiah cocok dipaketkan (*cross-selling*) dengan produk lambat tersebut untuk melikuidasi sisa stok.

---

### 2. Solusi Teknologi yang Ditawarkan (The Solution)

Untuk mengatasi permasalahan di atas, **Laf Project Smart POS** mengintegrasikan modul analisis transaksi cerdas langsung di dalam sistem kasir harian:

> [!TIP]
> **Transformasi POS Pasif Menjadi POS Cerdas**
> Sistem ini tidak hanya mencatat transaksi penjualan, tetapi juga memproses riwayat transaksi tersebut menggunakan metode *Association Rules Mining* secara otomatis.

#### A. Kenapa Memilih Algoritma FP-Growth?
Banyak sistem analitik tradisional menggunakan **Algoritma Apriori** untuk melakukan analisis asosiasi. Namun, untuk skala industri ritel yang membutuhkan respons cepat, Apriori terbukti lambat karena harus melakukan pemindaian basis data berulang kali (*multi-scan*) dan menghasilkan ribuan kandidat itemset sementara yang membebani memori server.

Sistem Laf Project menerapkan **Algoritma FP-Growth** karena memiliki keunggulan rekayasa perangkat lunak sebagai berikut:
1.  **Dua Kali Scan Database (High Efficiency):** FP-Growth mengompresi data transaksi ke dalam struktur pohon padat bernama **FP-Tree (Frequent Pattern Tree)**. Pemindaian database hanya dilakukan dua kali, sehingga waktu komputasi menjadi sangat singkat.
2.  **Tanpa Generate Kandidat (No Candidate Generation):** FP-Growth tidak perlu membuat kombinasi *k-itemset* teoritis di memori. Algoritma ini langsung menambang *frequent patterns* dari FP-Tree menggunakan strategi *Divide and Conquer*, yang menghemat penggunaan RAM server hingga 80% dibandingkan Apriori.
3.  **Parameter Fleksibel:** Pengguna (Owner/Manajer) dapat menentukan ambang batas **Minimum Support (%)** dan **Minimum Confidence (%)** secara interaktif untuk memfilter kekuatan hubungan antar-produk sesuai dengan kebutuhan musiman.

---

### 3. Dampak Setelah Implementasi (The Future Impact)

Setelah sistem ini diimplementasikan di Laf Project, dampak positif yang dirasakan mencakup aspek operasional hingga finansial:

| Aspek | Sebelum Implementasi | Setelah Implementasi (Dengan FP-Growth) | Dampak Bisnis (Business Impact) |
| :--- | :--- | :--- | :--- |
| **Pengambilan Keputusan** | Berdasarkan insting dan tebakan subjektif pemilik. | Berbasis data ilmiah (*Data-Driven*) menggunakan metrik *Support* & *Confidence*. | Akurasi keputusan mendekati 100%, meminimalisir kegagalan promo baru. |
| **Strategi Promosi** | Promo tunggal atau bundling acak yang kurang diminati. | Paket promo *Cross-Selling* presisi tinggi yang dibeli bersama secara natural. | Peningkatan rata-rata nilai transaksi per pelanggan (*Average Order Value*). |
| **Manajemen Stok** | Barang *slow-moving* menumpuk di gudang dan menjadi *dead stock*. | Melikuidasi barang *slow-moving* dengan memaketkannya secara cerdas dengan *fast-moving*. | Perputaran persediaan barang menjadi lebih cepat (*Inventory Turnover* meningkat). |
| **Efisiensi Pemasaran** | Pengeluaran biaya iklan besar untuk promo yang tidak laku. | Pemasaran terfokus pada kombinasi barang yang terbukti disukai pelanggan. | Efisiensi *Marketing Budget* dan ROI iklan meningkat tajam. |
| **Tata Letak Produk** | Penataan rak/gudang didasarkan pada estetika visual semata. | Barang dengan asosiasi tinggi diletakkan berdampingan untuk mendorong pembelian impulsif. | Pengalaman belanja pelanggan lebih praktis dan meningkatkan kenyamanan toko. |

---

### 4. Batasan & Lingkup Sistem (System Scope)
*   Sistem difokuskan pada pengolahan riwayat penjualan ritel Laf Project (khususnya tabel `penjualan` dan `detail_penjualan`).
*   Hasil komputasi algoritma menyajikan nilai metrik asosiasi (**Support**, **Confidence**, dan frekuensi item) untuk merekomendasikan paket promo berpasangan (*2-itemset bundling*).
*   Aplikasi tidak mencakup prediksi masa depan (*forecasting*) menggunakan Time Series, melainkan murni fokus pada pola asosiasi transaksi masa lalu (*Market Basket Analysis*).

---

## 🛠️ Tech Stack & Arsitektur Sistem

Sistem ini dirancang menggunakan arsitektur monolitik modern berkinerja tinggi dengan pemisahan tanggung jawab (*Separation of Concerns*) yang jelas antara Presentasi, Logika Bisnis, dan Manajemen Data.

```
                  ┌─────────────────────────────────────────┐
                  │              VITE / NPM                 │
                  │   Tailwind CSS v4.0  |  AlpineJS        │
                  └────────────────────┬────────────────────┘
                                       │ (Assets Render)
                                       ▼
  ┌────────────────────────────────────────────────────────────────────────┐
  │                           LARAVEL 12 FRAMEWORK                         │
  │                                                                        │
  │     ┌───────────────────────┐            ┌───────────────────────┐     │
  │     │   ROUTE & CONTROLLER  │ ─────────> │   BUSINESS SERVICES   │     │
  │     │  (FpGrowthController) │            │   (FpGrowthService)   │     │
  │     └───────────────────────┘            └───────────────────────┘     │
  │                 │                                    │                 │
  │                 ▼ (Uses Models)                      │ (Algorithmic)   │
  │     ┌───────────────────────┐                        │                 │
  │     │      ELOQUENT ORM     │ <──────────────────────┘                 │
  │     │   (Penjualan, Produk) │                                          │
  │     └───────────────────────┘                                          │
  └─────────────────┬──────────────────────────────────────────────────────┘
                    │
                    ▼ (Transactional Queries)
  ┌────────────────────────────────────────────────────────────────────────┐
  │                            DATABASE (MySQL)                            │
  │   - penjuallan                                                         │
  │   - detail_penjualan                                                   │
  │   - produk                                                             │
  └────────────────────────────────────────────────────────────────────────┘
```

*   **Backend Framework:** **Laravel 12.x** (PHP ^8.2) — Memanfaatkan arsitektur MVC yang solid, Eloquent ORM yang cepat untuk relasi database, dan *Service Layer* terpisah untuk isolasi algoritma FP-Growth.
*   **Data Mining Engine:** Custom Service PHP (`App\Services\FpGrowthService`) — Ditulis secara murni dan efisien untuk meminimalkan ketergantungan library eksternal, memastikan kecepatan eksekusi tinggi pada PHP 8.2+.
*   **Database:** **MySQL 8.x** — Dengan perancangan skema relasional yang terindeks secara optimal untuk mendukung kueri relasional berskala besar antara data struk transaksi dan barang.
*   **Frontend Engine:** **Tailwind CSS v4.0** & **Alpine.js 3.x** — Antarmuka dashboard yang mewah, modern, responsif, serta kasir interaktif dengan micro-interactions yang responsif.
*   **Ekspor Data:** `barryvdh/laravel-dompdf` (Cetak struk dan laporan PDF) & `maatwebsite/excel` (Ekspor analitik transaksi ke format spreadsheet Excel).

---

## 🔑 Fitur Utama (Core Features)

Sistem Informasi POS Laf Project dilengkapi dengan modul-modul modular berikut:

### 1. Modul Analisis Asosiasi Cerdas (FP-Growth Engine)
*   **Custom Parameter Input:** Pengguna dapat menentukan rentang tanggal transaksi yang ingin dianalisis, serta mengatur persentase batas **Minimum Support** dan **Minimum Confidence**.
*   **Real-time Processing:** Memproses ribuan baris transaksi secara instan dan menampilkan daftar aturan asosiasi (Contoh: *"Jika konsumen membeli **Laf Urban Flip**, maka peluang mereka membeli **Tali Sandal Gunung** adalah **85%**"*).
*   **Sorting & Filtering:** Hasil analisis dapat diurutkan berdasarkan metrik kegunaan terbesar untuk mempermudah pemilihan strategi bundling terbaik.

### 2. Modul Kasir Interaktif (Reactive Point of Sales)
*   Antarmuka kasir berbasis SPA (*Single Page Application*) yang cepat dengan Alpine.js.
*   Pencarian barang cepat, penyesuaian kuantitas instan, dan perhitungan kembalian otomatis.
*   Integrasi printer kasir untuk cetak struk penjualan real-time.

### 3. Modul Manajemen Inventaris Multi-Lokasi & Alur Barang
*   Pencatatan persediaan barang secara komprehensif berdasarkan kategori, satuan, dan lokasi gudang.
*   Fitur **Stok Minimum Alert**: Memberikan notifikasi visual darurat apabila stok barang tertentu berada di bawah batas minimum agar terhindar dari *Out of Stock*.
*   Pencatatan barang masuk (*restock*) dan keluar secara terstruktur dengan sistem *Purchase Order (PO)*.

### 4. Dashboard & Visualisasi Analitik (Executive Dashboard)
*   Grafik tren pendapatan mingguan/bulanan interaktif.
*   Indikator performa utama (KPI) seperti total pendapatan, total transaksi, rata-rata keranjang belanja, dan jumlah produk aktif.
*   Panel visualisasi produk terlaris dan promo teraktif saat ini.

---

## 🧠 Penjelasan Matematis Algoritma FP-Growth

Algoritma FP-Growth bekerja dengan mengevaluasi aturan asosiasi menggunakan tiga metrik utama:

### 1. Support
Support mengukur seberapa sering suatu kombinasi barang muncul dalam keseluruhan riwayat transaksi.
$$\text{Support}(A \rightarrow B) = \frac{\text{Jumlah Transaksi Berisi } A \text{ dan } B}{\text{Total Transaksi}}$$

### 2. Confidence
Confidence mengukur seberapa kuat hubungan antar-barang, yaitu peluang pelanggan membeli barang B setelah membeli barang A.
$$\text{Confidence}(A \rightarrow B) = \frac{\text{Jumlah Transaksi Berisi } A \text{ dan } B}{\text{Jumlah Transaksi Berisi } A}$$

---

## 📂 Struktur Database Relasional Utama

Untuk mendukung analisis berkinerja tinggi, database disusun dengan skema relasional terindeks di tabel penentu:

```sql
-- Skema Relasi Utama Penjualan & Detail Penjualan
CREATE TABLE `penjualan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nomor_transaksi` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `total_harga` decimal(15,2) NOT NULL,
  `kasir_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `penjualan_tanggal_index` (`tanggal`) -- Indeks krusial untuk rentang tanggal FP-Growth
);

CREATE TABLE `detail_penjualan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `penjualan_id` bigint unsigned NOT NULL,
  `produk_id` bigint unsigned NOT NULL,
  `jumlah` int NOT NULL,
  `harga_satuan` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`penjualan_id`) REFERENCES `penjualan` (`id`) ON DELETE CASCADE,
  KEY `detail_penjualan_produk_index` (`produk_id`)
);
```

---

## 🚀 Panduan Instalasi Lokal (Local Setup)

Ikuti langkah-langkah berikut untuk menjalankan project ini di server lokal Anda:

### 1. Prasyarat Sistem
*   PHP >= 8.2
*   Composer installed
*   Node.js (LTS version) & NPM
*   MySQL Server 8.x

### 2. Kloning Repositori
```bash
git clone https://github.com/SidikWaluyaa/Laf-Project.git
cd Laf-Project
```

### 3. Jalankan Script Setup Otomatis
Project ini dilengkapi dengan script instalasi otomatis sekali jalan. Jalankan perintah berikut:
```bash
composer run setup
```
*Script di atas akan secara otomatis menginstal dependensi PHP, membuat file `.env`, menghasilkan application key, menjalankan migrasi database, menginstal paket NPM, dan membuild aset frontend.*

### 4. Konfigurasi Environment (`.env`)
Buka file `.env` di root directory Anda dan sesuaikan koneksi database MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laf_project
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan Seeder Transaksi (Optional but Recommended)
Untuk menguji performa FP-Growth dengan data transaksi tiruan berskala besar, jalankan seeder database:
```bash
php artisan db:seed
```

### 6. Jalankan Server Pengembangan
Gunakan perintah concurrent terintegrasi untuk menjalankan Laravel Artisan dan Vite Dev Server secara bersamaan:
```bash
npm run dev
```
Buka browser Anda dan akses `http://127.0.0.1:8000` atau port yang tertera pada terminal.

---

## 🎨 Portofolio Showcase Note

> [!NOTE]
> **Mengapa Project Ini Sangat Layak untuk Dilirik Rekrut?**
> - **Penyelesaian Masalah Nyata:** Bukan sekadar aplikasi CRUD (Create, Read, Update, Delete) biasa. Project ini memecahkan masalah pemasaran riil di industri ritel menggunakan pendekatan ilmiah *Data Mining*.
> - **Implementasi Algoritma Murni:** Algoritma FP-Growth diimplementasikan secara mandiri (*custom algorithm*) di `App\Services\FpGrowthService` untuk mendemonstrasikan pemahaman mendalam tentang struktur data pohon (*Prefix Tree*) dan optimasi memori.
> - **Arsitektur Enterprise Modern:** Memperlihatkan penggunaan Laravel 12 terbaru dengan standar kode profesional yang bersih (*Clean Code*), pemisahan logika bisnis yang rapi, dan interface visual modern yang memanjakan mata menggunakan Tailwind CSS v4.0.

---
Ditulis dan dirancang dengan penuh dedikasi sebagai bukti portofolio rekayasa perangkat lunak berskala industri.
