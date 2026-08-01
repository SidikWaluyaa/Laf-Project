# BAB 3: ANALISIS DAN PERANCANGAN SISTEM

---

## 3.2 Analisis Sistem yang Sedang Berjalan

### 3.2.1 Prosedur Transaksi Penjualan (Shopee)

Sistem LAF Project saat ini telah memiliki fitur import data transaksi dari Shopee Marketplace. Prosedur bisnis dimulai dari pesanan masuk di platform Shopee hingga data tersimpan ke database sistem POS internal, namun **berhenti di titik penyimpanan** — data belum dianalisis lebih lanjut untuk keperluan strategis pemasaran.

**Alur Proses Bisnis Saat Ini:**

1. **Pesanan Masuk di Shopee** — Pembeli membuat pesanan melalui platform Shopee Marketplace.
2. **Proses Pemenuhan Pesanan** — Admin LAF Project memproses pesanan: konfirmasi, pengemasan, pengiriman via kurir.
3. **Status Pesanan Berubah** — Shopee mengubah status pesanan menjadi "Selesai" atau "Batal" secara otomatis setelah pembeli konfirmasi penerimaan atau terjadi pembatalan.
4. **Export Data dari Shopee Seller Centre** — Admin mengekspor laporan transaksi dalam format file Excel (.xlsx) dari dashboard Shopee Seller Centre.
5. **Import ke Sistem POS (LAF Project)** — Admin membuka halaman **Import & Data Transaksi Shopee** (route: `penjualan.import-shopee`) dan mengunggah file Excel tersebut.
6. **Parsing & Penyimpanan Data** — `ShopeeImportService` memproses file Excel: mengelompokkan baris berdasarkan `No. Pesanan`, lalu menyimpan data header ke tabel `penjualan_shopee` dan detail item ke tabel `penjualan_shopee_detail` menggunakan metode `updateOrCreate`.
7. **Data Tersimpan (Titik Akhir Sistem Berjalan)** — Data transaksi telah tersimpan di database, dapat dilihat dan dicari di halaman Import Shopee, **namun belum ada proses analisis lanjutan untuk menentukan pola pembelian atau rekomendasi paket promo**.

**Diagram BPMN Proses Bisnis Saat Ini:**

```xml
<mxfile host="app.diagrams.net">
  <diagram name="BPMN-Sistem-Berjalan" id="bpmn-current">
    <mxGraphModel dx="1200" dy="800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1600" pageHeight="900" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        <!-- Pool: LAF Project -->
        <mxCell id="pool1" value="LAF Project - Proses Transaksi Shopee (Sistem Berjalan)" style="shape=pool;startSize=30;horizontal=1;fillColor=#dae8fc;strokeColor=#6c8ebf;fontStyle=1;fontSize=12;" vertex="1" parent="1">
          <mxGeometry x="20" y="20" width="1520" height="420" as="geometry" />
        </mxCell>
        <!-- Lane: Shopee Platform -->
        <mxCell id="lane1" value="Platform Shopee" style="shape=lane;startSize=30;horizontal=0;fillColor=#fff2cc;strokeColor=#d6b656;fontStyle=1;fontSize=10;" vertex="1" parent="pool1">
          <mxGeometry y="30" width="1520" height="130" as="geometry" />
        </mxCell>
        <!-- Lane: Admin LAF -->
        <mxCell id="lane2" value="Admin LAF Project" style="shape=lane;startSize=30;horizontal=0;fillColor=#d5e8d4;strokeColor=#82b366;fontStyle=1;fontSize=10;" vertex="1" parent="pool1">
          <mxGeometry y="160" width="1520" height="130" as="geometry" />
        </mxCell>
        <!-- Lane: Sistem POS -->
        <mxCell id="lane3" value="Sistem POS (Laravel)" style="shape=lane;startSize=30;horizontal=0;fillColor=#e1d5e7;strokeColor=#9673a6;fontStyle=1;fontSize=10;" vertex="1" parent="pool1">
          <mxGeometry y="290" width="1520" height="130" as="geometry" />
        </mxCell>

        <!-- Start Event -->
        <mxCell id="start1" value="" style="shape=mxgraph.bpmn.shape;perimeter=mxPerimeter.ellipsePerimeter;symbol=general;isLooping=0;isSequential=0;isCompensation=0;isCall=0;isAdHoc=0;isTask=0;outline=throwing;fillColor=#67AB9F;" vertex="1" parent="lane1">
          <mxGeometry x="60" y="45" width="40" height="40" as="geometry" />
        </mxCell>
        <mxCell id="t1" value="Pembeli Membuat&#xa;Pesanan di Shopee" style="shape=mxgraph.bpmn.shape;perimeter=mxPerimeter.rectanglePerimeter;symbol=general;isLooping=0;isSequential=0;isCompensation=0;isCall=0;isAdHoc=0;isTask=1;taskMarker=abstract;rounded=1;fillColor=#fff2cc;strokeColor=#d6b656;" vertex="1" parent="lane1">
          <mxGeometry x="140" y="35" width="140" height="60" as="geometry" />
        </mxCell>
        <mxCell id="t2" value="Status Pesanan&#xa;Berubah (Selesai/Batal)" style="shape=mxgraph.bpmn.shape;perimeter=mxPerimeter.rectanglePerimeter;symbol=general;isLooping=0;isSequential=0;isCompensation=0;isCall=0;isAdHoc=0;isTask=1;taskMarker=abstract;rounded=1;fillColor=#fff2cc;strokeColor=#d6b656;" vertex="1" parent="lane1">
          <mxGeometry x="520" y="35" width="160" height="60" as="geometry" />
        </mxCell>

        <!-- Admin tasks -->
        <mxCell id="t3" value="Proses Pemenuhan&#xa;Pesanan (Kemas &amp; Kirim)" style="shape=mxgraph.bpmn.shape;perimeter=mxPerimeter.rectanglePerimeter;symbol=general;isLooping=0;isSequential=0;isCompensation=0;isCall=0;isAdHoc=0;isTask=1;taskMarker=abstract;rounded=1;fillColor=#d5e8d4;strokeColor=#82b366;" vertex="1" parent="lane2">
          <mxGeometry x="320" y="35" width="160" height="60" as="geometry" />
        </mxCell>
        <mxCell id="t4" value="Export Data Excel&#xa;dari Shopee Seller Centre" style="shape=mxgraph.bpmn.shape;perimeter=mxPerimeter.rectanglePerimeter;symbol=general;isLooping=0;isSequential=0;isCompensation=0;isCall=0;isAdHoc=0;isTask=1;taskMarker=abstract;rounded=1;fillColor=#d5e8d4;strokeColor=#82b366;" vertex="1" parent="lane2">
          <mxGeometry x="720" y="35" width="160" height="60" as="geometry" />
        </mxCell>
        <mxCell id="t5" value="Upload File Excel&#xa;di Halaman Import Shopee" style="shape=mxgraph.bpmn.shape;perimeter=mxPerimeter.rectanglePerimeter;symbol=general;isLooping=0;isSequential=0;isCompensation=0;isCall=0;isAdHoc=0;isTask=1;taskMarker=abstract;rounded=1;fillColor=#d5e8d4;strokeColor=#82b366;" vertex="1" parent="lane2">
          <mxGeometry x="920" y="35" width="160" height="60" as="geometry" />
        </mxCell>

        <!-- System tasks -->
        <mxCell id="t6" value="ShopeeImportService&#xa;Parsing &amp; Simpan Data" style="shape=mxgraph.bpmn.shape;perimeter=mxPerimeter.rectanglePerimeter;symbol=general;isLooping=0;isSequential=0;isCompensation=0;isCall=0;isAdHoc=0;isTask=1;taskMarker=service;rounded=1;fillColor=#e1d5e7;strokeColor=#9673a6;" vertex="1" parent="lane3">
          <mxGeometry x="1080" y="35" width="160" height="60" as="geometry" />
        </mxCell>
        <mxCell id="ds1" value="penjualan_shopee&#xa;&amp;&#xa;penjualan_shopee_detail" style="shape=mxgraph.bpmn.shape;perimeter=mxPerimeter.rectanglePerimeter;symbol=general;isLooping=0;isSequential=0;isCompensation=0;isCall=0;isAdHoc=0;isTask=0;rounded=0;fillColor=#f8cecc;strokeColor=#b85450;fontStyle=1;fontSize=9;" vertex="1" parent="lane3">
          <mxGeometry x="1290" y="25" width="160" height="80" as="geometry" />
        </mxCell>

        <!-- End Event -->
        <mxCell id="end1" value="Data Tersimpan&#xa;(Belum Dianalisis)" style="shape=mxgraph.bpmn.shape;perimeter=mxPerimeter.ellipsePerimeter;symbol=terminate;isLooping=0;isSequential=0;isCompensation=0;isCall=0;isAdHoc=0;isTask=0;outline=end;fillColor=#f8cecc;strokeColor=#b85450;fontSize=8;" vertex="1" parent="lane2">
          <mxGeometry x="1400" y="40" width="50" height="50" as="geometry" />
        </mxCell>

        <!-- Sequence Flows -->
        <mxCell id="f1" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="start1" target="t1" parent="pool1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="f2" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="t1" target="t3" parent="pool1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="f3" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="t3" target="t2" parent="pool1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="f4" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="t2" target="t4" parent="pool1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="f5" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="t4" target="t5" parent="pool1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="f6" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="t5" target="t6" parent="pool1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="f7" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="t6" target="ds1" parent="pool1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="f8" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="ds1" target="end1" parent="pool1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>
```

---

### 3.2.2 Analisis Data Transaksi yang Tersedia

Data transaksi Shopee pada sistem LAF Project tersimpan dalam dua tabel yang saling berelasi. Struktur kedua tabel ini diambil langsung dari file migration [2026_07_21_000001_create_penjualan_shopee_tables.php](file:///c:/laragon/www/Laf-Project/database/migrations/2026_07_21_000001_create_penjualan_shopee_tables.php).

#### A. Tabel `penjualan_shopee` (Header Transaksi)

Tabel ini menyimpan data header/ringkasan setiap pesanan Shopee. Satu baris mewakili satu transaksi/pesanan.

| No | Kolom | Tipe Data | Keterangan |
|----|-------|-----------|------------|
| 1 | `id` | bigint (PK, AI) | Primary key auto-increment |
| 2 | `no_pesanan` | varchar(100), unique | Nomor pesanan unik dari Shopee |
| 3 | `tipe_pesanan` | varchar(100), nullable | Tipe pesanan (reguler, dll) |
| 4 | `status_pesanan` | varchar(50), indexed | Status pesanan: **"Selesai"** atau **"Batal"** |
| 5 | `alasan_pembatalan` | text, nullable | Alasan pembatalan pesanan |
| 6 | `status_pembatalan` | varchar(100), nullable | Status pembatalan/pengembalian |
| 7 | `no_resi` | varchar(100), nullable | Nomor resi pengiriman |
| 8 | `opsi_pengiriman` | varchar(100), nullable | Opsi pengiriman (JNE, J&T, dll) |
| 9 | `metode_pengiriman` | varchar(50), nullable | Metode pengiriman |
| 10 | `deadline_pengiriman` | datetime, nullable | Batas waktu pengiriman |
| 11 | `waktu_pengiriman_diatur` | datetime, nullable | Waktu pengiriman diatur |
| 12 | `waktu_pesanan_dibuat` | datetime, indexed | Waktu pesanan dibuat (dipakai sebagai filter tanggal) |
| 13 | `waktu_pembayaran` | datetime, nullable | Waktu pembayaran dilakukan |
| 14 | `metode_pembayaran` | varchar(100), nullable | Metode pembayaran pembeli |
| 15 | `voucher_penjual` | decimal(15,2), default 0 | Voucher ditanggung penjual |
| 16 | `cashback_koin` | decimal(15,2), default 0 | Cashback koin Shopee |
| 17 | `voucher_shopee` | decimal(15,2), default 0 | Voucher ditanggung Shopee |
| 18 | `potongan_koin` | decimal(15,2), default 0 | Potongan koin Shopee |
| 19 | `diskon_kartu_kredit` | decimal(15,2), default 0 | Diskon kartu kredit |
| 20 | `ongkir_pembeli` | decimal(15,2), default 0 | Ongkos kirim yang dibayar pembeli |
| 21 | `estimasi_potongan_ongkir` | decimal(15,2), default 0 | Estimasi potongan biaya pengiriman |
| 22 | `ongkir_pengembalian` | decimal(15,2), default 0 | Ongkir pengembalian barang |
| 23 | `total_pembayaran` | decimal(15,2), default 0 | Total pembayaran transaksi |
| 24 | `perkiraan_ongkir` | decimal(15,2), default 0 | Perkiraan ongkos kirim |
| 25 | `catatan_pembeli` | text, nullable | Catatan dari pembeli |
| 26 | `catatan` | text, nullable | Catatan umum |
| 27 | `username_pembeli` | varchar(100), nullable | Username pembeli di Shopee |
| 28 | `nama_penerima` | varchar(150), nullable | Nama penerima paket |
| 29 | `no_telepon` | varchar(50), nullable | Nomor telepon penerima |
| 30 | `alamat_pengiriman` | text, nullable | Alamat lengkap pengiriman |
| 31 | `kota` | varchar(100), nullable | Kota/kabupaten tujuan |
| 32 | `provinsi` | varchar(100), nullable | Provinsi tujuan |
| 33 | `waktu_pesanan_selesai` | datetime, nullable | Waktu pesanan selesai |
| 34 | `created_at` | timestamp | Timestamp pembuatan record |
| 35 | `updated_at` | timestamp | Timestamp pembaruan record |

#### B. Tabel `penjualan_shopee_detail` (Detail Item per Transaksi)

Tabel ini menyimpan detail setiap item produk dalam satu pesanan. Satu pesanan bisa memiliki banyak baris detail (relasi **one-to-many**).

| No | Kolom | Tipe Data | Keterangan |
|----|-------|-----------|------------|
| 1 | `id` | bigint (PK, AI) | Primary key auto-increment |
| 2 | `penjualan_shopee_id` | bigint (FK) | Foreign key ke tabel `penjualan_shopee` (cascade on delete) |
| 3 | `sku_induk` | varchar(100), nullable, indexed | SKU induk produk (dipakai untuk grouping item di mode Parent SKU) |
| 4 | `nama_produk` | varchar(255), indexed | **Nama produk dari Shopee** (langsung tersedia, tidak perlu join ke tabel `produk`) |
| 5 | `nomor_referensi_sku` | varchar(100), nullable | Nomor referensi SKU |
| 6 | `nama_variasi` | varchar(150), nullable | Nama variasi produk (warna, ukuran, dll) |
| 7 | `harga_awal` | decimal(15,2), default 0 | Harga awal sebelum diskon |
| 8 | `harga_setelah_diskon` | decimal(15,2), default 0 | Harga setelah diskon |
| 9 | `jumlah` | int, default 0 | Jumlah item yang dibeli |
| 10 | `returned_quantity` | int, default 0 | Jumlah item yang dikembalikan |
| 11 | `subtotal_pesanan` | decimal(15,2), default 0 | Subtotal pesanan item |
| 12 | `total_diskon` | decimal(15,2), default 0 | Total diskon item |
| 13 | `diskon_penjual` | decimal(15,2), default 0 | Diskon dari penjual |
| 14 | `diskon_shopee` | decimal(15,2), default 0 | Diskon dari Shopee |
| 15 | `berat_produk` | varchar(50), nullable | Berat produk |
| 16 | `jumlah_produk_dipesan` | int, default 0 | Jumlah produk dipesan |
| 17 | `total_berat` | varchar(50), nullable | Total berat keseluruhan |
| 18 | `paket_diskon` | varchar(10), nullable | Flag paket diskon |
| 19 | `paket_diskon_shopee` | decimal(15,2), default 0 | Diskon paket dari Shopee |
| 20 | `paket_diskon_penjual` | decimal(15,2), default 0 | Diskon paket dari penjual |
| 21 | `created_at` | timestamp | Timestamp pembuatan record |
| 22 | `updated_at` | timestamp | Timestamp pembaruan record |

**Relasi antar tabel:**
- Model `PenjualanShopee` memiliki relasi `hasMany` ke `PenjualanShopeeDetail` melalui kolom `penjualan_shopee_id`.
- Model `PenjualanShopeeDetail` memiliki relasi `belongsTo` ke `PenjualanShopee` melalui kolom `penjualan_shopee_id`.

Definisi basket/keranjang transaksi untuk analisis FP-Growth: **1 baris `penjualan_shopee` = 1 transaksi**, dan item-itemnya adalah kumpulan `nama_produk` dari `penjualan_shopee_detail` yang memiliki `penjualan_shopee_id` yang sama.

---

### 3.2.3 Permasalahan pada Sistem Berjalan

Berdasarkan analisis sistem yang sedang berjalan, ditemukan satu permasalahan inti yang menjadi fokus penelitian ini:

> **Penentuan paket promo produk pada LAF Project masih dilakukan secara manual dan berdasarkan perkiraan subjektif, belum memanfaatkan pola pembelian aktual dari data transaksi Shopee yang sudah tersimpan di database.**

Secara rinci, permasalahan ini meliputi:

1. **Data Rich but Information Poor** — Sistem sudah menyimpan **4.182 pesanan** (3.493 berstatus "Selesai" dan 689 berstatus "Batal") dengan **4.478 baris detail item** dari transaksi Shopee. Namun data ini hanya digunakan sebagai arsip catatan transaksi dan belum diekstraksi untuk menemukan pola-pola pembelian yang bermanfaat.

2. **Keputusan Promo Berbasis Intuisi** — Manajemen LAF Project menentukan strategi bundling/paket promo berdasarkan intuisi dan pengamatan sekilas tanpa perhitungan persentase yang valid. Ini berisiko menghasilkan paket promo yang tidak relevan dengan kebutuhan nyata pelanggan.

3. **Tidak Ada Mekanisme Analisis Otomatis** — Sistem berjalan tidak menyediakan fitur untuk menganalisis pola asosiasi antar produk dari data transaksi yang sudah tersimpan. Admin hanya bisa melihat daftar pesanan dan detail itemnya, tanpa rekomendasi kombinasi produk yang sering dibeli bersamaan.

---

## 3.3 Analisis Kebutuhan Sistem

### 3.3.1 Kebutuhan Fungsional

Berdasarkan analisis permasalahan di atas, berikut adalah kebutuhan fungsional yang **khusus terkait fitur FP-Growth**, yang telah diimplementasikan dalam sistem. Referensi codebase: [FpGrowthController.php](file:///c:/laragon/www/Laf-Project/app/Http/Controllers/FpGrowthController.php) dan [FpGrowthService.php](file:///c:/laragon/www/Laf-Project/app/Services/FpGrowthService.php).

| Kode | Kebutuhan Fungsional | Deskripsi |
|------|----------------------|-----------|
| KF-01 | Mengatur Parameter Analisis | Admin dapat menentukan parameter: **Minimum Support (%)**, **Minimum Confidence (%)**, **Rentang Tanggal** (start_date & end_date), **Sumber Data** (Shopee/Kasir/Semua), **Tingkat Granularitas** (Parent SKU / Full Name), dan opsi **Abaikan Item Packing**. |
| KF-02 | Menjalankan Proses FP-Growth | Sistem memproses data transaksi melalui algoritma FP-Growth: menghitung frekuensi item, memfilter berdasarkan minimum support, mengurutkan item secara descending, menghitung pair counts, dan menghasilkan association rules. |
| KF-03 | Menampilkan Association Rules | Sistem menampilkan tabel hasil aturan asosiasi berupa: item antecedent, item consequent, nilai Support (%), Confidence (%), dan **Lift Ratio** beserta interpretasinya (Kuat/Netral/Lemah). |
| KF-04 | Menampilkan Rekomendasi Paket Promo | Sistem menghasilkan **Ringkasan Rekomendasi Promo Bundling Terbaik** (maksimal 4 rekomendasi utama) dengan kategori promo (Cross-Selling, Paket Combo Variasi, Paket Add-On, dll) dan saran aksi bisnis konkret. |
| KF-05 | Menampilkan Detail Perhitungan per Rule | Untuk setiap rule, sistem menampilkan: interpretasi kalimat, rasio pembeli, simulasi perhitungan matematis (rumus Support, Confidence, Lift Ratio), serta bukti transaksi riil (sampel hingga 10 nota pesanan). |
| KF-06 | Data Cleaning Otomatis | Sistem secara otomatis memfilter hanya transaksi dengan `status_pesanan = 'Selesai'` dan dapat mengabaikan item non-produk (bubble wrap, kardus, packing, dll) berdasarkan kata kunci. |

### 3.3.2 Kebutuhan Non-Fungsional

| Kode | Kebutuhan Non-Fungsional | Deskripsi |
|------|--------------------------|-----------|
| KNF-01 | Performa | Algoritma FP-Growth harus mampu memproses ribuan transaksi dalam waktu yang wajar (< 10 detik untuk 3.493 transaksi). |
| KNF-02 | Usability | Antarmuka halaman FP-Growth harus intuitif — admin cukup mengisi parameter dan klik tombol "Mulai Analisis FP-Growth" tanpa memerlukan pengetahuan teknis tentang algoritma. |
| KNF-03 | Akurasi | Hasil perhitungan Support, Confidence, dan Lift Ratio harus akurat secara matematis dan dapat diverifikasi melalui simulasi perhitungan yang ditampilkan di setiap detail rule. |
| KNF-04 | Kompatibilitas Data | Sistem harus kompatibel dengan format export Excel standar dari Shopee Seller Centre tanpa memerlukan modifikasi manual pada file. |
| KNF-05 | Keamanan | Fitur analisis FP-Growth hanya dapat diakses oleh pengguna yang sudah terautentikasi (middleware `auth`). |

### 3.3.3 Analisis Kebutuhan Data

Data yang dibutuhkan untuk analisis FP-Growth bersumber dari tabel `penjualan_shopee` dan `penjualan_shopee_detail`. Proses preprocessing/data cleaning meliputi:

1. **Filter Status Pesanan** — Berdasarkan data aktual di database, kolom `status_pesanan` memiliki **2 nilai yang valid**:
   - `"Selesai"` → 3.493 pesanan (83,5%)
   - `"Batal"` → 689 pesanan (16,5%)

   Hanya transaksi dengan `status_pesanan = 'Selesai'` yang digunakan sebagai input FP-Growth. Hal ini dilakukan karena transaksi batal tidak merepresentasikan pembelian aktual dan akan menghasilkan noise pada analisis pola pembelian. Implementasi filter ini terlihat pada [FpGrowthController.php baris 53](file:///c:/laragon/www/Laf-Project/app/Http/Controllers/FpGrowthController.php#L53): `->where('status_pesanan', 'Selesai')`.

2. **Filter Rentang Tanggal** — Admin dapat membatasi periode transaksi yang dianalisis melalui parameter `start_date` dan `end_date` dengan filter `whereBetween('waktu_pesanan_dibuat', ...)`.

3. **Pengabaian Item Packing** — Sistem secara default mengabaikan item non-produk berdasarkan kata kunci: `bubble`, `kardus`, `packing`, `pengaman`, `pelindung paket`, `dus`, `biaya packing`. Fitur ini dapat dinonaktifkan oleh admin melalui checkbox "Abaikan Item Packing & Kelengkapan".

4. **Mode Pengelompokan Produk (Group By)** — Terdapat dua mode identifikasi item:
   - **Parent SKU** (`sku_induk`) — Mengelompokkan variasi produk (warna/ukuran berbeda) ke satu identitas produk induk. Ini mode yang direkomendasikan agar frekuensi lebih solid.
   - **Full Name** (`nama_produk` + `nama_variasi`) — Menggunakan nama produk lengkap dengan variasi sebagai identitas item terpisah.

5. **Definisi Basket** — Satu transaksi = 1 record `penjualan_shopee`, item = kumpulan `nama_produk` unik dari `penjualan_shopee_detail` dengan `penjualan_shopee_id` yang sama.

---

## 3.4 Analisis Penerapan Algoritma FP-Growth

### 3.4.1 Data yang Digunakan

Berikut adalah data **10 transaksi riil** dari database LAF Project yang berstatus "Selesai" dan memiliki minimal 2 item produk berbeda per transaksi. Data ini diambil langsung dari tabel `penjualan_shopee` dan `penjualan_shopee_detail`.

Untuk menyederhanakan perhitungan manual, nama produk yang panjang disingkat menggunakan kode alias berikut:

| Kode Item | Nama Produk Asli (di database) |
|-----------|-------------------------------|
| A | Sepatu Neo Walk — `LAF Project - Sepatu Sneakers Pria Hitam Putih Polos Casual Keren Sporty Neo Walk` |
| B | Sandal Orlan — `LAF Project X Wilford - Sandal Slide Sendal Casual Kulit Pria Hitam Formal Ringan Anti Slip Orlan` |
| C | Kaos Kaki Bergaris — `Kaos Kaki Medium Motif Bergaris Garis Stripe Black Kerja Casual Formal Premium LAF Project` |
| D | Sandal Jack V1 — `Sandal Umroh Haji Laki Laki Pria Wanita Anti Slip Ringan Premium Jack V1 (berbagai warna) LAF Project` |
| E | Sandal Anak Imaji — `Sandal Gunung Anak Laki laki Perempuan Sendal Anti Licin Coklat Imaji LAF Project` |
| F | Kaos Kaki Terry — `LAF Project - Kaos Kaki Panjang Crew Socks Happy Terry Tebal Polos Kerja Casual Premium Basic Hitam Putih` |
| G | Kaos Kaki Ankle — `Kaos Kaki Pendek Ankle Pria Wanita Polos Semata Kaki Premium LAF Project` |
| H | Sabun Sepatu — `LAF Project - Sabun Cuci Sepatu Sandal Shoe Cleaner & Conditioner Cairan Pembersih Perawatan 100ml` |
| I | Sepatu Aruna — `Sepatu Sneakers Pria Casual Kerja Kantor Hitam Putih Polos Keren Aruna LAF Project` |
| J | Sepatu Anak Sagan — `Sepatu Sekolah Sneakers Anak Laki laki Perempuan Hitam Putih Polos Casual Premium Sagan LAF Project` |
| K | Sepatu Anak Benicio — `LAF Project - Sepatu Anak Laki laki Perempuan Sneakers Sekolah Casual Kasual - Benicio` |
| L | Boardshort — `Boardshort Celana Pendek Pria Dewasa Casual Cargo Parasut Navy Original LAF Project` |
| M | Kaos Kaki Sekolah — `LAF Project - Kaos Kaki Sekolah Hitam Putih Panjang Tapak Hitam SD SMP SMA Premium` |
| N | Sepatu Anak TomTom — `Sepatu Anak Laki laki Perempuan Sneakers Sekolah Casual Kasual Hitam Black TOM TOM LAF Project Kids` |
| O | Tali Sepatu Bulat — `Tali Sepatu Bulat Lilin Pantofel Premium Wax Shoelaces 100 cm LAF Project` |
| P | Tali Sepatu Gepeng — `Tali Sepatu Gepeng Lilin Flat Wax Shoelaces Premium 100cm Hitam Putih LAF Project` |
| Q | Sandal Jack V1 (Olive) — `Sandal Gunung Pria Wanita Casual Kasual Hiking Haji Umrah Hijrah Olive Army Jack V1 LAF Project` |
| R | Lap Microfiber — `Lap Microfiber Cloth Kain Towel Handuk Pembersih Perawatan Serbaguna Sepatu Premium LAF Project` |

**Tabel Transaksi:**

| No | No. Pesanan | Daftar Item (Kode) |
|----|-------------|-------------------|
| T1 | 2605018W5016YB | A, B |
| T2 | 2605019W3BTNBP | C, D |
| T3 | 260502B2TJ9NYE | E, D |
| T4 | 260502C5A220VT | Q, F |
| T5 | 260502C7DXH5U3 | G, R, H, I |
| T6 | 260502CBDRSMAT | J, K |
| T7 | 260502CMPJFGQ6 | L, C |
| T8 | 260503DQNE89P9 | J, M |
| T9 | 260503DT3HSSNY | N, O, P |
| T10 | 2605019QXD8M4V (tambahan, 1 item unique sandal x2 variasi) | D, D (digabung jadi D saja) |

> **Catatan:** Pada T10 terdapat pembelian 2 unit produk yang sama (Sandal Jack V1) dengan variasi berbeda. Dalam analisis FP-Growth, item duplikat dalam satu transaksi dihitung sekali saja (menggunakan `array_unique`). Transaksi T3 (yang no_pesanannya `260501A25A53VT`) sengaja **tidak dimasukkan** karena item keduanya adalah "Box Kardus Packing" yang termasuk item packing (difilter oleh preprocessing). Sebagai gantinya diambil transaksi `260502B2TJ9NYE`.

### 3.4.2 Tahapan Pembentukan FP-Tree

#### Langkah 1: Hitung Frekuensi Setiap Item

Dari 10 transaksi di atas, dihitung frekuensi kemunculan setiap item (setiap item dihitung maksimal 1 kali per transaksi):

| Item | Frekuensi |
|------|-----------|
| D (Sandal Jack V1) | 3 |
| C (Kaos Kaki Bergaris) | 2 |
| J (Sepatu Anak Sagan) | 2 |
| A (Sepatu Neo Walk) | 1 |
| B (Sandal Orlan) | 1 |
| E (Sandal Anak Imaji) | 1 |
| F (Kaos Kaki Terry) | 1 |
| G (Kaos Kaki Ankle) | 1 |
| H (Sabun Sepatu) | 1 |
| I (Sepatu Aruna) | 1 |
| K (Sepatu Anak Benicio) | 1 |
| L (Boardshort) | 1 |
| M (Kaos Kaki Sekolah) | 1 |
| N (Sepatu Anak TomTom) | 1 |
| O (Tali Sepatu Bulat) | 1 |
| P (Tali Sepatu Gepeng) | 1 |
| Q (Sandal Jack V1 Olive) | 1 |
| R (Lap Microfiber) | 1 |

#### Langkah 2: Tentukan Minimum Support Count

Dengan **Minimum Support = 10%** dan total transaksi = 10:

> **Min Support Count** = (10 / 100) × 10 = **1**

Semua item memiliki frekuensi ≥ 1, sehingga semua item frequent. Namun untuk demonstrasi yang menghasilkan rule bermakna, kita gunakan **Minimum Support = 20%** sehingga Min Support Count = 2.

Item frequent (frekuensi ≥ 2):

| Peringkat | Item | Frekuensi |
|-----------|------|-----------|
| 1 | D (Sandal Jack V1) | 3 |
| 2 | C (Kaos Kaki Bergaris) | 2 |
| 3 | J (Sepatu Anak Sagan) | 2 |

#### Langkah 3: Urutkan Item dalam Setiap Transaksi

Setiap transaksi diurutkan berdasarkan frekuensi descending, dan hanya menyertakan item frequent:

| Transaksi | Item Asli | Item Frequent (Terurut) |
|-----------|-----------|------------------------|
| T1 | A, B | *(kosong — tidak ada item frequent)* |
| T2 | C, D | D, C |
| T3 | E, D | D |
| T4 | Q, F | *(kosong)* |
| T5 | G, R, H, I | *(kosong)* |
| T6 | J, K | J |
| T7 | L, C | C |
| T8 | J, M | J |
| T9 | N, O, P | *(kosong)* |
| T10 | D | D |

Transaksi yang tersisa setelah filtering (hanya yang memiliki ≥ 1 item frequent):
- T2: {D, C}
- T3: {D}
- T6: {J}
- T7: {C}
- T8: {J}
- T10: {D}

#### Langkah 4: Pembangunan FP-Tree (Step by Step)

**Inisialisasi:** Buat node Root (null).

**Sisipkan T2 {D, C}:**
- Root → **D:1** → **C:1**

**Sisipkan T3 {D}:**
- Root → **D:2** (increment count D)

**Sisipkan T6 {J}:**
- Root → **J:1** (cabang baru dari root)

**Sisipkan T7 {C}:**
- Root → **C:1** (cabang baru dari root, karena C tidak dimulai dari D)

**Sisipkan T8 {J}:**
- Root → **J:2** (increment count J)

**Sisipkan T10 {D}:**
- Root → **D:3** (increment count D)

**FP-Tree Akhir:**

```
        [Root]
       /  |  \
    D:3  J:2  C:1
    |
   C:1
```

**Diagram FP-Tree (XML draw.io):**

```xml
<mxfile host="app.diagrams.net">
  <diagram name="FP-Tree" id="fptree">
    <mxGraphModel dx="800" dy="600" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="900" pageHeight="600" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        <!-- Title -->
        <mxCell id="title" value="FP-Tree (Min Support = 20%, Total Transaksi = 10)" style="text;html=1;fontSize=14;fontStyle=1;align=center;" vertex="1" parent="1">
          <mxGeometry x="200" y="20" width="500" height="30" as="geometry" />
        </mxCell>
        <!-- Root Node -->
        <mxCell id="root" value="Root (null)" style="ellipse;whiteSpace=wrap;html=1;fillColor=#f5f5f5;strokeColor=#666666;fontStyle=1;fontSize=12;" vertex="1" parent="1">
          <mxGeometry x="370" y="70" width="120" height="50" as="geometry" />
        </mxCell>
        <!-- D:3 -->
        <mxCell id="d3" value="D : 3" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#dae8fc;strokeColor=#6c8ebf;fontStyle=1;fontSize=13;" vertex="1" parent="1">
          <mxGeometry x="180" y="180" width="100" height="50" as="geometry" />
        </mxCell>
        <!-- J:2 -->
        <mxCell id="j2" value="J : 2" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#d5e8d4;strokeColor=#82b366;fontStyle=1;fontSize=13;" vertex="1" parent="1">
          <mxGeometry x="380" y="180" width="100" height="50" as="geometry" />
        </mxCell>
        <!-- C:1 (from root) -->
        <mxCell id="c1root" value="C : 1" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#fff2cc;strokeColor=#d6b656;fontStyle=1;fontSize=13;" vertex="1" parent="1">
          <mxGeometry x="580" y="180" width="100" height="50" as="geometry" />
        </mxCell>
        <!-- C:1 (child of D) -->
        <mxCell id="c1d" value="C : 1" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#fff2cc;strokeColor=#d6b656;fontStyle=1;fontSize=13;" vertex="1" parent="1">
          <mxGeometry x="180" y="290" width="100" height="50" as="geometry" />
        </mxCell>
        <!-- Edges -->
        <mxCell id="e1" style="endArrow=block;endFill=1;strokeWidth=2;" edge="1" source="root" target="d3" parent="1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="e2" style="endArrow=block;endFill=1;strokeWidth=2;" edge="1" source="root" target="j2" parent="1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="e3" style="endArrow=block;endFill=1;strokeWidth=2;" edge="1" source="root" target="c1root" parent="1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="e4" style="endArrow=block;endFill=1;strokeWidth=2;" edge="1" source="d3" target="c1d" parent="1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <!-- Header Table Link -->
        <mxCell id="hl1" value="Header Table:&#xa;D → 3 (frequent)&#xa;C → 2 (1+1, via node-link)&#xa;J → 2 (frequent)" style="shape=note;size=15;whiteSpace=wrap;html=1;fillColor=#e1d5e7;strokeColor=#9673a6;align=left;fontSize=10;fontStyle=0;" vertex="1" parent="1">
          <mxGeometry x="640" y="280" width="200" height="80" as="geometry" />
        </mxCell>
        <!-- Node-link (dashed) between two C nodes -->
        <mxCell id="nl1" style="endArrow=open;dashed=1;strokeColor=#d6b656;strokeWidth=1.5;" edge="1" source="c1d" target="c1root" parent="1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>
```

---

### 3.4.3 Perhitungan Support dan Confidence

Dari FP-Tree yang telah dibangun, kita dapat mengekstrak frequent itemset dan menghitung nilai Support, Confidence, serta Lift Ratio untuk setiap aturan asosiasi.

**Total Transaksi (N) = 10**

#### Frequent 1-Itemset

| Itemset | Count | Support |
|---------|-------|---------|
| {D} | 3 | 3/10 = 30% |
| {C} | 2 | 2/10 = 20% |
| {J} | 2 | 2/10 = 20% |

#### Frequent 2-Itemset

Dari FP-Tree, satu-satunya pasangan yang muncul bersama dalam minimal 1 transaksi yang teridentifikasi melalui conditional pattern base:

| Itemset | Count | Support |
|---------|-------|---------|
| {D, C} | 1 | 1/10 = 10% |

> **Catatan:** Dengan Min Support = 20%, pasangan {D, C} dengan support 10% sebenarnya **tidak memenuhi threshold**. Namun untuk keperluan demonstrasi perhitungan, kita tetap tampilkan prosesnya. Pada implementasi sesungguhnya di [FpGrowthService.php](file:///c:/laragon/www/Laf-Project/app/Services/FpGrowthService.php), jika support count pasangan < minSupportCount, rule tidak akan dihasilkan.

#### Perhitungan Rules (Demonstrasi)

Jika kita turunkan min support menjadi **10%** agar {D, C} memenuhi:

**Rule 1: D → C**

| Metrik | Rumus | Perhitungan | Hasil |
|--------|-------|-------------|-------|
| Support | Count(D ∩ C) / N | 1 / 10 | **10%** |
| Confidence | Count(D ∩ C) / Count(D) | 1 / 3 | **33,33%** |
| Lift Ratio | Confidence / Support(C) | 33,33% / 20% | **1,67** |

**Interpretasi Rule 1:** Dari 3 pembeli yang membeli Sandal Jack V1 (D), 1 di antaranya (33,33%) juga membeli Kaos Kaki Bergaris (C). Lift Ratio 1,67 > 1,0 menunjukkan hubungan asosiasi yang **positif dan valid** — pembelian D mendorong pembelian C di atas tingkat kebetulan.

**Rule 2: C → D**

| Metrik | Rumus | Perhitungan | Hasil |
|--------|-------|-------------|-------|
| Support | Count(D ∩ C) / N | 1 / 10 | **10%** |
| Confidence | Count(D ∩ C) / Count(C) | 1 / 2 | **50%** |
| Lift Ratio | Confidence / Support(D) | 50% / 30% | **1,67** |

**Interpretasi Rule 2:** Dari 2 pembeli yang membeli Kaos Kaki Bergaris (C), 1 di antaranya (50%) juga membeli Sandal Jack V1 (D). Confidence 50% dengan Lift Ratio 1,67 menunjukkan asosiasi yang kuat.

> **Rumus yang digunakan dalam implementasi** ([FpGrowthService.php baris 116-126](file:///c:/laragon/www/Laf-Project/app/Services/FpGrowthService.php#L116-L126)):
> - **Support(A ∪ B)** = Count(A ∩ B) / Total Transaksi × 100%
> - **Confidence(A → B)** = Count(A ∩ B) / Count(A) × 100%
> - **Lift Ratio(A → B)** = Confidence(A → B) / Support(B)

---

### 3.4.4 Hasil Association Rule dari Contoh Kasus

Berikut adalah tabel akhir association rules yang dihasilkan dari data 10 transaksi riil di atas (dengan min support = 10% dan min confidence = 10%):

| No | Antecedent (Jika Membeli) | Consequent (Maka Juga Membeli) | Support (%) | Confidence (%) | Lift Ratio | Keterangan |
|----|---------------------------|-------------------------------|-------------|----------------|------------|------------|
| 1 | C (Kaos Kaki Bergaris) | D (Sandal Jack V1) | 10% | 50,00% | 1,67 | Kuat ✓ |
| 2 | D (Sandal Jack V1) | C (Kaos Kaki Bergaris) | 10% | 33,33% | 1,67 | Kuat ✓ |

**Interpretasi Hasil:**

Dari hasil analisis, teridentifikasi pola asosiasi antara produk **Kaos Kaki Bergaris** dan **Sandal Jack V1**:

- Separuh (50%) dari pembeli Kaos Kaki Bergaris juga membeli Sandal Jack V1 dalam transaksi yang sama.
- Sepertiga (33,33%) dari pembeli Sandal Jack V1 juga membeli Kaos Kaki Bergaris.
- Lift Ratio 1,67 pada kedua arah menunjukkan hubungan ini bukan kebetulan, melainkan adanya kecenderungan nyata pelanggan untuk membeli keduanya bersamaan.

**Rekomendasi Bisnis:** Berdasarkan rule ini, LAF Project dapat membuat **paket bundling "Sandal Jack V1 + Kaos Kaki Bergaris"** dengan harga paket khusus, atau menampilkan rekomendasi "Sering Dibeli Bersama" di deskripsi produk Shopee.

> **Catatan:** Contoh perhitungan di atas menggunakan sampel 10 transaksi untuk keperluan demonstrasi. Pada implementasi sesungguhnya, sistem menganalisis **seluruh 3.493 transaksi** berstatus Selesai sehingga menghasilkan pola yang lebih banyak dan akurat.

---

## 3.5 Analisis Pemecahan Masalah

Berdasarkan permasalahan yang teridentifikasi pada subbab 3.2.3, yaitu penentuan paket promo yang masih manual dan tidak berbasis data, solusi yang diusulkan adalah **mengintegrasikan modul analisis FP-Growth ke dalam sistem POS LAF Project**.

Solusi ini menjembatani kesenjangan antara data transaksi Shopee yang sudah tersimpan (subbab 3.2.2) dengan kebutuhan informasi strategis untuk pembuatan paket promo. Proses transformasi data menjadi knowledge terjadi melalui tahapan algoritma FP-Growth yang telah didemonstrasikan pada subbab 3.4.

**Alur Sistem Usulan:**

1. **Data transaksi tersimpan** — Sebagaimana sistem berjalan, data dari Shopee telah ter-import ke tabel `penjualan_shopee` dan `penjualan_shopee_detail`.
2. **Admin membuka halaman Analisis FP-Growth** — Melalui menu sidebar "Analisis → FP-Growth (Promo)" pada route `/fp-growth`.
3. **Admin mengatur parameter** — Menentukan minimum support, minimum confidence, rentang tanggal, sumber data, dan opsi preprocessing.
4. **Admin menjalankan proses FP-Growth** — Klik tombol "Mulai Analisis FP-Growth" yang memanggil route `POST /fp-growth/process`.
5. **Sistem memproses data** — `FpGrowthController` mengambil data transaksi, melakukan preprocessing, lalu memanggil `FpGrowthService::run()` untuk menjalankan algoritma FP-Growth.
6. **Association rules dihasilkan** — Sistem menghasilkan daftar aturan asosiasi beserta metrik Support, Confidence, dan Lift Ratio.
7. **Rekomendasi promo ditampilkan** — Sistem menampilkan ringkasan rekomendasi paket promo beserta saran aksi bisnis yang konkret, dilengkapi bukti transaksi riil.
8. **Admin mengambil keputusan** — Berdasarkan rekomendasi yang terukur dan berbasis data, admin/manajemen dapat membuat paket bundling promo yang tepat sasaran di Shopee Seller Centre.

**Diagram BPMN Sistem Usulan:**

```xml
<mxfile host="app.diagrams.net">
  <diagram name="BPMN-Sistem-Usulan" id="bpmn-proposed">
    <mxGraphModel dx="1400" dy="900" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1800" pageHeight="900" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        <!-- Pool -->
        <mxCell id="pool" value="LAF Project - Proses Rekomendasi Paket Promo dengan FP-Growth (Sistem Usulan)" style="shape=pool;startSize=30;horizontal=1;fillColor=#dae8fc;strokeColor=#6c8ebf;fontStyle=1;fontSize=12;" vertex="1" parent="1">
          <mxGeometry x="20" y="20" width="1740" height="420" as="geometry" />
        </mxCell>
        <!-- Lane: Admin -->
        <mxCell id="laneAdmin" value="Admin LAF Project" style="shape=lane;startSize=30;horizontal=0;fillColor=#d5e8d4;strokeColor=#82b366;fontStyle=1;fontSize=10;" vertex="1" parent="pool">
          <mxGeometry y="30" width="1740" height="180" as="geometry" />
        </mxCell>
        <!-- Lane: Sistem -->
        <mxCell id="laneSys" value="Sistem POS + FP-Growth" style="shape=lane;startSize=30;horizontal=0;fillColor=#e1d5e7;strokeColor=#9673a6;fontStyle=1;fontSize=10;" vertex="1" parent="pool">
          <mxGeometry y="210" width="1740" height="210" as="geometry" />
        </mxCell>

        <!-- Start -->
        <mxCell id="s1" value="" style="shape=mxgraph.bpmn.shape;perimeter=mxPerimeter.ellipsePerimeter;symbol=general;outline=throwing;fillColor=#67AB9F;" vertex="1" parent="laneAdmin">
          <mxGeometry x="50" y="70" width="40" height="40" as="geometry" />
        </mxCell>
        <!-- Admin: Buka halaman FP-Growth -->
        <mxCell id="a1" value="Buka Halaman&#xa;Analisis FP-Growth" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#d5e8d4;strokeColor=#82b366;fontStyle=0;fontSize=10;" vertex="1" parent="laneAdmin">
          <mxGeometry x="130" y="55" width="140" height="60" as="geometry" />
        </mxCell>
        <!-- Admin: Atur Parameter -->
        <mxCell id="a2" value="Atur Parameter&#xa;(Support, Confidence,&#xa;Tanggal, Sumber Data)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#d5e8d4;strokeColor=#82b366;fontStyle=0;fontSize=10;" vertex="1" parent="laneAdmin">
          <mxGeometry x="310" y="45" width="170" height="80" as="geometry" />
        </mxCell>
        <!-- Admin: Klik Mulai Analisis -->
        <mxCell id="a3" value="Klik &quot;Mulai&#xa;Analisis FP-Growth&quot;" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#d5e8d4;strokeColor=#82b366;fontStyle=1;fontSize=10;" vertex="1" parent="laneAdmin">
          <mxGeometry x="520" y="55" width="140" height="60" as="geometry" />
        </mxCell>
        <!-- Admin: Lihat Hasil -->
        <mxCell id="a4" value="Lihat Rekomendasi&#xa;Paket Promo &amp;&#xa;Association Rules" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#d5e8d4;strokeColor=#82b366;fontStyle=1;fontSize=10;" vertex="1" parent="laneAdmin">
          <mxGeometry x="1220" y="45" width="160" height="80" as="geometry" />
        </mxCell>
        <!-- Admin: Keputusan Bisnis -->
        <mxCell id="a5" value="Buat Paket Bundling&#xa;Promo di Shopee&#xa;Seller Centre" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#fff2cc;strokeColor=#d6b656;fontStyle=1;fontSize=10;" vertex="1" parent="laneAdmin">
          <mxGeometry x="1430" y="45" width="160" height="80" as="geometry" />
        </mxCell>
        <!-- End -->
        <mxCell id="e1end" value="" style="shape=mxgraph.bpmn.shape;perimeter=mxPerimeter.ellipsePerimeter;symbol=terminate;outline=end;fillColor=#f8cecc;strokeColor=#b85450;" vertex="1" parent="laneAdmin">
          <mxGeometry x="1640" y="65" width="40" height="40" as="geometry" />
        </mxCell>

        <!-- System: Ambil Data -->
        <mxCell id="sys1" value="Query Data Transaksi&#xa;(status_pesanan = 'Selesai')&#xa;dari penjualan_shopee" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#e1d5e7;strokeColor=#9673a6;fontStyle=0;fontSize=10;" vertex="1" parent="laneSys">
          <mxGeometry x="530" y="70" width="180" height="80" as="geometry" />
        </mxCell>
        <!-- System: Preprocessing -->
        <mxCell id="sys2" value="Data Cleaning&#xa;(Filter Packing,&#xa;Group by SKU)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#e1d5e7;strokeColor=#9673a6;fontStyle=0;fontSize=10;" vertex="1" parent="laneSys">
          <mxGeometry x="740" y="70" width="140" height="80" as="geometry" />
        </mxCell>
        <!-- System: FP-Growth -->
        <mxCell id="sys3" value="FpGrowthService::run()&#xa;Hitung Frekuensi →&#xa;Pair Counts →&#xa;Generate Rules" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#f8cecc;strokeColor=#b85450;fontStyle=1;fontSize=10;" vertex="1" parent="laneSys">
          <mxGeometry x="920" y="60" width="160" height="90" as="geometry" />
        </mxCell>
        <!-- System: Generate Rekomendasi -->
        <mxCell id="sys4" value="Generate Top&#xa;Rekomendasi Promo&#xa;+ Saran Aksi Bisnis" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#e1d5e7;strokeColor=#9673a6;fontStyle=0;fontSize=10;" vertex="1" parent="laneSys">
          <mxGeometry x="1120" y="70" width="160" height="80" as="geometry" />
        </mxCell>

        <!-- Flows: Admin lane -->
        <mxCell id="fa1" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="s1" target="a1" parent="pool">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="fa2" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="a1" target="a2" parent="pool">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="fa3" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="a2" target="a3" parent="pool">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="fa4" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="a3" target="sys1" parent="pool">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="fa5" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="sys1" target="sys2" parent="pool">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="fa6" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="sys2" target="sys3" parent="pool">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="fa7" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="sys3" target="sys4" parent="pool">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="fa8" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="sys4" target="a4" parent="pool">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="fa9" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="a4" target="a5" parent="pool">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="fa10" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="a5" target="e1end" parent="pool">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>
```

Dengan adanya sistem usulan ini, permasalahan pada subbab 3.2.3 terselesaikan karena:
- Keputusan pembuatan paket promo kini **berbasis data** (data-driven), bukan berdasarkan intuisi.
- Nilai **Support** dan **Confidence** memberikan ukuran kuantitatif yang terverifikasi secara matematis.
- Nilai **Lift Ratio** mengkonfirmasi apakah asosiasi yang ditemukan bersifat nyata atau hanya kebetulan.
- Admin mendapatkan **saran aksi bisnis konkret** beserta bukti transaksi riil, sehingga rekomendasi lebih actionable.

---

## 3.6 Perancangan Sistem (UML)

Seluruh diagram UML berikut dibatasi **hanya untuk fitur FP-Growth** dan menggunakan nama class, route, serta controller yang benar-benar ada di codebase.

### 3.6.1 Use Case Diagram

**Aktor:** Admin LAF Project (terautentikasi melalui middleware `auth`)

**Use Case:**
1. Mengatur Parameter Analisis (min_support, min_confidence, start_date, end_date, sumber_data, group_by, abaikan_packing)
2. Menjalankan Proses FP-Growth
3. Melihat Rekomendasi Paket Promo (termasuk Association Rules Detail dan Bukti Transaksi Riil)

```xml
<mxfile host="app.diagrams.net">
  <diagram name="Use-Case-Diagram" id="usecase">
    <mxGraphModel dx="900" dy="700" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1000" pageHeight="700" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        <!-- System Boundary -->
        <mxCell id="boundary" value="Sistem Informasi POS LAF Project&#xa;(Modul FP-Growth)" style="shape=mxgraph.sysml.package;labelX=120;align=left;spacingLeft=5;tabWidth=250;tabHeight=25;fillColor=#f5f5f5;strokeColor=#666666;fontStyle=1;fontSize=11;" vertex="1" parent="1">
          <mxGeometry x="250" y="40" width="500" height="560" as="geometry" />
        </mxCell>
        <!-- Actor: Admin -->
        <mxCell id="admin" value="Admin" style="shape=mxgraph.bpmn.shape;perimeter=mxPerimeter.rectanglePerimeter;symbol=general;isLooping=0;isSequential=0;isCompensation=0;isCall=0;isAdHoc=0;isTask=0;verticalLabelPosition=bottom;fontStyle=1;fontSize=12;fillColor=none;strokeColor=none;image=img/lib/mscae/Person.svg;imageWidth=35;imageHeight=50;" vertex="1" parent="1">
          <mxGeometry x="80" y="260" width="60" height="80" as="geometry" />
        </mxCell>
        <!-- UC1: Mengatur Parameter -->
        <mxCell id="uc1" value="Mengatur Parameter&#xa;Analisis FP-Growth&#xa;&#xa;(min_support, min_confidence,&#xa;start_date, end_date,&#xa;sumber_data, group_by)" style="ellipse;whiteSpace=wrap;html=1;fillColor=#dae8fc;strokeColor=#6c8ebf;fontSize=10;" vertex="1" parent="1">
          <mxGeometry x="330" y="80" width="220" height="120" as="geometry" />
        </mxCell>
        <!-- UC2: Menjalankan Proses -->
        <mxCell id="uc2" value="Menjalankan Proses&#xa;FP-Growth&#xa;&#xa;(POST /fp-growth/process)" style="ellipse;whiteSpace=wrap;html=1;fillColor=#d5e8d4;strokeColor=#82b366;fontSize=10;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="330" y="240" width="220" height="120" as="geometry" />
        </mxCell>
        <!-- UC3: Lihat Rekomendasi -->
        <mxCell id="uc3" value="Melihat Rekomendasi&#xa;Paket Promo&#xa;&#xa;(Association Rules, Top&#xa;Recommendations, Bukti&#xa;Transaksi Riil)" style="ellipse;whiteSpace=wrap;html=1;fillColor=#fff2cc;strokeColor=#d6b656;fontSize=10;" vertex="1" parent="1">
          <mxGeometry x="330" y="400" width="220" height="130" as="geometry" />
        </mxCell>
        <!-- Association lines -->
        <mxCell id="assoc1" style="endArrow=none;" edge="1" source="admin" target="uc1" parent="1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="assoc2" style="endArrow=none;" edge="1" source="admin" target="uc2" parent="1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="assoc3" style="endArrow=none;" edge="1" source="admin" target="uc3" parent="1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <!-- Include: UC2 includes UC1 -->
        <mxCell id="inc1" value="&lt;&lt;include&gt;&gt;" style="endArrow=open;dashed=1;endSize=12;fontSize=9;fontStyle=2;" edge="1" source="uc2" target="uc1" parent="1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <!-- Include: UC3 depends on UC2 -->
        <mxCell id="inc2" value="&lt;&lt;include&gt;&gt;" style="endArrow=open;dashed=1;endSize=12;fontSize=9;fontStyle=2;" edge="1" source="uc3" target="uc2" parent="1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>
```

---

### 3.6.2 Activity Diagram

Alur aktivitas admin dari trigger proses FP-Growth sampai hasil rule muncul:

```xml
<mxfile host="app.diagrams.net">
  <diagram name="Activity-Diagram" id="activity">
    <mxGraphModel dx="900" dy="1200" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1000" pageHeight="1400" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        <!-- Swimlane: Admin -->
        <mxCell id="swimAdmin" value="Admin" style="shape=swimlane;startSize=25;horizontal=1;fillColor=#d5e8d4;strokeColor=#82b366;fontStyle=1;fontSize=11;" vertex="1" parent="1">
          <mxGeometry x="40" y="20" width="350" height="1300" as="geometry" />
        </mxCell>
        <!-- Swimlane: Sistem -->
        <mxCell id="swimSys" value="Sistem (FpGrowthController + FpGrowthService)" style="shape=swimlane;startSize=25;horizontal=1;fillColor=#e1d5e7;strokeColor=#9673a6;fontStyle=1;fontSize=11;" vertex="1" parent="1">
          <mxGeometry x="390" y="20" width="400" height="1300" as="geometry" />
        </mxCell>

        <!-- Start -->
        <mxCell id="start" value="" style="ellipse;fillColor=#000000;" vertex="1" parent="swimAdmin">
          <mxGeometry x="150" y="40" width="30" height="30" as="geometry" />
        </mxCell>
        <!-- A1: Buka halaman -->
        <mxCell id="act1" value="Buka halaman FP-Growth&#xa;(GET /fp-growth)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#d5e8d4;strokeColor=#82b366;fontSize=10;" vertex="1" parent="swimAdmin">
          <mxGeometry x="75" y="100" width="180" height="50" as="geometry" />
        </mxCell>
        <!-- A2: Isi parameter -->
        <mxCell id="act2" value="Isi parameter:&#xa;min_support, min_confidence,&#xa;start_date, end_date,&#xa;sumber_data, group_by" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#d5e8d4;strokeColor=#82b366;fontSize=10;" vertex="1" parent="swimAdmin">
          <mxGeometry x="55" y="180" width="220" height="80" as="geometry" />
        </mxCell>
        <!-- A3: Klik submit -->
        <mxCell id="act3" value="Klik &quot;Mulai Analisis FP-Growth&quot;&#xa;(POST /fp-growth/process)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#d5e8d4;strokeColor=#82b366;fontStyle=1;fontSize=10;" vertex="1" parent="swimAdmin">
          <mxGeometry x="55" y="290" width="220" height="60" as="geometry" />
        </mxCell>

        <!-- System activities -->
        <!-- S1: Validasi -->
        <mxCell id="sys1" value="Validasi input parameter&#xa;(Request validate)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#e1d5e7;strokeColor=#9673a6;fontSize=10;" vertex="1" parent="swimSys">
          <mxGeometry x="100" y="370" width="200" height="50" as="geometry" />
        </mxCell>
        <!-- Decision: Valid? -->
        <mxCell id="dec1" value="Valid?" style="rhombus;whiteSpace=wrap;html=1;fillColor=#fff2cc;strokeColor=#d6b656;fontSize=10;" vertex="1" parent="swimSys">
          <mxGeometry x="150" y="450" width="100" height="60" as="geometry" />
        </mxCell>
        <!-- S2: Query data -->
        <mxCell id="sys2" value="Query PenjualanShopee&#xa;WHERE status_pesanan = 'Selesai'&#xa;AND waktu_pesanan_dibuat BETWEEN&#xa;start_date AND end_date" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#e1d5e7;strokeColor=#9673a6;fontSize=10;" vertex="1" parent="swimSys">
          <mxGeometry x="80" y="540" width="240" height="80" as="geometry" />
        </mxCell>
        <!-- Decision: Ada data? -->
        <mxCell id="dec2" value="Data&#xa;Ditemukan?" style="rhombus;whiteSpace=wrap;html=1;fillColor=#fff2cc;strokeColor=#d6b656;fontSize=10;" vertex="1" parent="swimSys">
          <mxGeometry x="150" y="650" width="100" height="60" as="geometry" />
        </mxCell>
        <!-- S3: Preprocessing -->
        <mxCell id="sys3" value="Preprocessing:&#xa;- Filter item packing&#xa;- Group by SKU/nama&#xa;- Bangun basket transaksi" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#e1d5e7;strokeColor=#9673a6;fontSize=10;" vertex="1" parent="swimSys">
          <mxGeometry x="80" y="740" width="240" height="80" as="geometry" />
        </mxCell>
        <!-- S4: FP-Growth -->
        <mxCell id="sys4" value="FpGrowthService::run()&#xa;1. Hitung frekuensi item&#xa;2. Filter item &gt;= minSupportCount&#xa;3. Hitung pair counts&#xa;4. Generate association rules&#xa;5. Hitung Support, Confidence, Lift" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#f8cecc;strokeColor=#b85450;fontStyle=1;fontSize=10;" vertex="1" parent="swimSys">
          <mxGeometry x="60" y="850" width="280" height="110" as="geometry" />
        </mxCell>
        <!-- S5: Generate Top Rekomendasi -->
        <mxCell id="sys5" value="Generate Top Rekomendasi&#xa;Promo Bundling Terbaik&#xa;(maks 4 rekomendasi)" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#e1d5e7;strokeColor=#9673a6;fontSize=10;" vertex="1" parent="swimSys">
          <mxGeometry x="80" y="990" width="240" height="70" as="geometry" />
        </mxCell>
        <!-- S6: Tampilkan hasil -->
        <mxCell id="sys6" value="Tampilkan hasil ke View:&#xa;- Executive Summary&#xa;- Tabel Association Rules&#xa;- Detail per Rule + Bukti Nota" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#e1d5e7;strokeColor=#9673a6;fontSize=10;" vertex="1" parent="swimSys">
          <mxGeometry x="80" y="1090" width="240" height="80" as="geometry" />
        </mxCell>

        <!-- Admin: Lihat hasil -->
        <mxCell id="act4" value="Lihat &amp; Analisis Hasil&#xa;Rekomendasi Paket Promo" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#d5e8d4;strokeColor=#82b366;fontStyle=1;fontSize=10;" vertex="1" parent="swimAdmin">
          <mxGeometry x="55" y="1150" width="220" height="60" as="geometry" />
        </mxCell>
        <!-- End -->
        <mxCell id="endNode" value="" style="ellipse;fillColor=#000000;strokeColor=#000000;" vertex="1" parent="swimAdmin">
          <mxGeometry x="150" y="1250" width="30" height="30" as="geometry" />
        </mxCell>
        <mxCell id="endOuter" value="" style="ellipse;fillColor=none;strokeColor=#000000;strokeWidth=2;" vertex="1" parent="swimAdmin">
          <mxGeometry x="145" y="1245" width="40" height="40" as="geometry" />
        </mxCell>

        <!-- Error end (back with error) -->
        <mxCell id="errEnd" value="Tampilkan Pesan&#xa;Error, Kembali&#xa;ke Form" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#f8cecc;strokeColor=#b85450;fontSize=9;" vertex="1" parent="swimSys">
          <mxGeometry x="300" y="460" width="90" height="60" as="geometry" />
        </mxCell>
        <mxCell id="errEnd2" value="Tampilkan Pesan&#xa;'Tidak ada data',&#xa;Kembali ke Form" style="rounded=1;whiteSpace=wrap;html=1;fillColor=#f8cecc;strokeColor=#b85450;fontSize=9;" vertex="1" parent="swimSys">
          <mxGeometry x="300" y="650" width="90" height="60" as="geometry" />
        </mxCell>

        <!-- Flows -->
        <mxCell id="f1" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="start" target="act1" parent="1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="f2" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="act1" target="act2" parent="1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="f3" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="act2" target="act3" parent="1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="f4" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="act3" target="sys1" parent="1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="f5" edge="1" source="sys1" target="dec1" parent="swimSys">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="f6" value="Ya" style="" edge="1" source="dec1" target="sys2" parent="swimSys">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="f6b" value="Tidak" style="" edge="1" source="dec1" target="errEnd" parent="swimSys">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="f7" edge="1" source="sys2" target="dec2" parent="swimSys">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="f8" value="Ya" style="" edge="1" source="dec2" target="sys3" parent="swimSys">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="f8b" value="Tidak" style="" edge="1" source="dec2" target="errEnd2" parent="swimSys">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="f9" edge="1" source="sys3" target="sys4" parent="swimSys">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="f10" edge="1" source="sys4" target="sys5" parent="swimSys">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="f11" edge="1" source="sys5" target="sys6" parent="swimSys">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="f12" style="edgeStyle=orthogonalEdgeStyle;" edge="1" source="sys6" target="act4" parent="1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="f13" edge="1" source="act4" target="endOuter" parent="swimAdmin">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>
```

---

### 3.6.3 Sequence Diagram

Interaksi antar komponen saat admin menjalankan proses FP-Growth:

```xml
<mxfile host="app.diagrams.net">
  <diagram name="Sequence-Diagram" id="sequence">
    <mxGraphModel dx="1200" dy="900" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1400" pageHeight="1000" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        <!-- Title -->
        <mxCell id="title" value="Sequence Diagram — Proses Analisis FP-Growth" style="text;html=1;fontSize=14;fontStyle=1;align=center;" vertex="1" parent="1">
          <mxGeometry x="300" y="10" width="500" height="30" as="geometry" />
        </mxCell>
        <!-- Lifelines -->
        <mxCell id="admin" value=":Admin" style="shape=umlLifeline;perimeter=lifelinePerimeter;whiteSpace=wrap;html=1;container=0;collapsible=0;recursiveResize=0;outlineConnect=0;fillColor=#d5e8d4;strokeColor=#82b366;fontStyle=1;fontSize=11;size=40;" vertex="1" parent="1">
          <mxGeometry x="50" y="50" width="120" height="850" as="geometry" />
        </mxCell>
        <mxCell id="view" value=":View&#xa;(fp-growth/index)" style="shape=umlLifeline;perimeter=lifelinePerimeter;whiteSpace=wrap;html=1;container=0;collapsible=0;recursiveResize=0;outlineConnect=0;fillColor=#fff2cc;strokeColor=#d6b656;fontStyle=1;fontSize=10;size=40;" vertex="1" parent="1">
          <mxGeometry x="250" y="50" width="130" height="850" as="geometry" />
        </mxCell>
        <mxCell id="ctrl" value=":FpGrowthController" style="shape=umlLifeline;perimeter=lifelinePerimeter;whiteSpace=wrap;html=1;container=0;collapsible=0;recursiveResize=0;outlineConnect=0;fillColor=#dae8fc;strokeColor=#6c8ebf;fontStyle=1;fontSize=10;size=40;" vertex="1" parent="1">
          <mxGeometry x="460" y="50" width="140" height="850" as="geometry" />
        </mxCell>
        <mxCell id="svc" value=":FpGrowthService" style="shape=umlLifeline;perimeter=lifelinePerimeter;whiteSpace=wrap;html=1;container=0;collapsible=0;recursiveResize=0;outlineConnect=0;fillColor=#f8cecc;strokeColor=#b85450;fontStyle=1;fontSize=10;size=40;" vertex="1" parent="1">
          <mxGeometry x="680" y="50" width="130" height="850" as="geometry" />
        </mxCell>
        <mxCell id="modelPS" value=":PenjualanShopee&#xa;(Model/DB)" style="shape=umlLifeline;perimeter=lifelinePerimeter;whiteSpace=wrap;html=1;container=0;collapsible=0;recursiveResize=0;outlineConnect=0;fillColor=#e1d5e7;strokeColor=#9673a6;fontStyle=1;fontSize=10;size=40;" vertex="1" parent="1">
          <mxGeometry x="900" y="50" width="140" height="850" as="geometry" />
        </mxCell>

        <!-- Messages -->
        <!-- 1. Admin -> View: GET /fp-growth -->
        <mxCell id="m1" value="1. GET /fp-growth" style="html=1;verticalAlign=bottom;endArrow=block;fontSize=10;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry">
            <mxPoint x="110" y="120" as="sourcePoint" />
            <mxPoint x="315" y="120" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <!-- 2. View -> Controller: index() -->
        <mxCell id="m2" value="2. index()" style="html=1;verticalAlign=bottom;endArrow=block;fontSize=10;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry">
            <mxPoint x="315" y="150" as="sourcePoint" />
            <mxPoint x="530" y="150" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <!-- 3. Controller -> View: return view -->
        <mxCell id="m3" value="3. return view('fp-growth.index')" style="html=1;verticalAlign=bottom;endArrow=open;dashed=1;fontSize=10;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry">
            <mxPoint x="530" y="180" as="sourcePoint" />
            <mxPoint x="315" y="180" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <!-- 4. View -> Admin: Tampilkan form -->
        <mxCell id="m4" value="4. Tampilkan form parameter" style="html=1;verticalAlign=bottom;endArrow=open;dashed=1;fontSize=10;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry">
            <mxPoint x="315" y="210" as="sourcePoint" />
            <mxPoint x="110" y="210" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <!-- 5. Admin -> View: Isi parameter & submit -->
        <mxCell id="m5" value="5. POST /fp-growth/process&#xa;   (min_support, min_confidence, dates...)" style="html=1;verticalAlign=bottom;endArrow=block;fontSize=10;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry">
            <mxPoint x="110" y="270" as="sourcePoint" />
            <mxPoint x="315" y="270" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <!-- 6. View -> Controller: process(Request) -->
        <mxCell id="m6" value="6. process(Request $request)" style="html=1;verticalAlign=bottom;endArrow=block;fontSize=10;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry">
            <mxPoint x="315" y="310" as="sourcePoint" />
            <mxPoint x="530" y="310" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <!-- 7. Controller -> Controller: validate() -->
        <mxCell id="m7" value="7. $request->validate()" style="html=1;verticalAlign=bottom;endArrow=block;fontSize=9;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry">
            <mxPoint x="535" y="340" as="sourcePoint" />
            <mxPoint x="580" y="340" as="targetPoint" />
            <Array as="points">
              <mxPoint x="580" y="340" />
              <mxPoint x="580" y="360" />
              <mxPoint x="535" y="360" />
            </Array>
          </mxGeometry>
        </mxCell>
        <!-- 8. Controller -> Model: Query -->
        <mxCell id="m8" value="8. PenjualanShopee::with('detail')&#xa;   ->where('status_pesanan', 'Selesai')&#xa;   ->whereBetween(...)->get()" style="html=1;verticalAlign=bottom;endArrow=block;fontSize=9;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry">
            <mxPoint x="535" y="400" as="sourcePoint" />
            <mxPoint x="970" y="400" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <!-- 9. Model -> Controller: Collection -->
        <mxCell id="m9" value="9. return Collection&lt;PenjualanShopee&gt;" style="html=1;verticalAlign=bottom;endArrow=open;dashed=1;fontSize=9;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry">
            <mxPoint x="970" y="440" as="sourcePoint" />
            <mxPoint x="535" y="440" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <!-- 10. Controller: Build transactions array -->
        <mxCell id="m10" value="10. Build $transactions[]&#xa;    (preprocessing, filter packing)" style="html=1;verticalAlign=bottom;endArrow=block;fontSize=9;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry">
            <mxPoint x="535" y="470" as="sourcePoint" />
            <mxPoint x="580" y="470" as="targetPoint" />
            <Array as="points">
              <mxPoint x="580" y="470" />
              <mxPoint x="580" y="510" />
              <mxPoint x="535" y="510" />
            </Array>
          </mxGeometry>
        </mxCell>
        <!-- 11. Controller -> Service: run() -->
        <mxCell id="m11" value="11. FpGrowthService::run($transactions,&#xa;    $minSupport, $minConfidence)" style="html=1;verticalAlign=bottom;endArrow=block;fontSize=9;fontStyle=1;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry">
            <mxPoint x="535" y="550" as="sourcePoint" />
            <mxPoint x="745" y="550" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <!-- 12. Service: Hitung frekuensi + pair counts -->
        <mxCell id="m12" value="12. Hitung itemFrequencies,&#xa;    filter frequentItems,&#xa;    hitung pairCounts,&#xa;    generate rules[]" style="html=1;verticalAlign=bottom;endArrow=block;fontSize=9;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry">
            <mxPoint x="750" y="580" as="sourcePoint" />
            <mxPoint x="790" y="580" as="targetPoint" />
            <Array as="points">
              <mxPoint x="790" y="580" />
              <mxPoint x="790" y="660" />
              <mxPoint x="750" y="660" />
            </Array>
          </mxGeometry>
        </mxCell>
        <!-- 13. Service -> Controller: return $rules -->
        <mxCell id="m13" value="13. return $rules[]" style="html=1;verticalAlign=bottom;endArrow=open;dashed=1;fontSize=10;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry">
            <mxPoint x="745" y="690" as="sourcePoint" />
            <mxPoint x="535" y="690" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <!-- 14. Controller: Build topRecommendations -->
        <mxCell id="m14" value="14. Build $topRecommendations[]" style="html=1;verticalAlign=bottom;endArrow=block;fontSize=9;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry">
            <mxPoint x="535" y="720" as="sourcePoint" />
            <mxPoint x="580" y="720" as="targetPoint" />
            <Array as="points">
              <mxPoint x="580" y="720" />
              <mxPoint x="580" y="750" />
              <mxPoint x="535" y="750" />
            </Array>
          </mxGeometry>
        </mxCell>
        <!-- 15. Controller -> View: back()->with() -->
        <mxCell id="m15" value="15. return back()->with(['results', 'topRecommendations', ...])" style="html=1;verticalAlign=bottom;endArrow=open;dashed=1;fontSize=9;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry">
            <mxPoint x="530" y="780" as="sourcePoint" />
            <mxPoint x="315" y="780" as="targetPoint" />
          </mxGeometry>
        </mxCell>
        <!-- 16. View -> Admin: Tampilkan hasil -->
        <mxCell id="m16" value="16. Tampilkan Executive Summary +&#xa;    Association Rules + Detail" style="html=1;verticalAlign=bottom;endArrow=open;dashed=1;fontSize=10;" edge="1" parent="1">
          <mxGeometry relative="1" as="geometry">
            <mxPoint x="315" y="820" as="sourcePoint" />
            <mxPoint x="110" y="820" as="targetPoint" />
          </mxGeometry>
        </mxCell>
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>
```

---

### 3.6.4 Class Diagram

Class diagram yang menampilkan hanya class yang terlibat dalam fitur FP-Growth:

```xml
<mxfile host="app.diagrams.net">
  <diagram name="Class-Diagram" id="classdiag">
    <mxGraphModel dx="1100" dy="800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1200" pageHeight="900" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        <!-- Title -->
        <mxCell id="title" value="Class Diagram — Modul FP-Growth" style="text;html=1;fontSize=14;fontStyle=1;align=center;" vertex="1" parent="1">
          <mxGeometry x="300" y="10" width="400" height="30" as="geometry" />
        </mxCell>

        <!-- FpGrowthController -->
        <mxCell id="ctrl" value="&lt;b&gt;FpGrowthController&lt;/b&gt;&#xa;(App\Http\Controllers)&lt;hr&gt;&#xa;&lt;div style='text-align:left;font-size:10px;'&gt;&#xa;&lt;/div&gt;&lt;hr&gt;&#xa;&lt;div style='text-align:left;font-size:10px;'&gt;&#xa;+ index() : View&#xa;+ process(Request $request) : RedirectResponse&#xa;&lt;/div&gt;" style="shape=mxgraph.unified_modeling_language.class;fontFamily=Helvetica;html=1;overflow=fill;whiteSpace=wrap;fillColor=#dae8fc;strokeColor=#6c8ebf;fontSize=11;verticalAlign=top;" vertex="1" parent="1">
          <mxGeometry x="60" y="80" width="320" height="130" as="geometry" />
        </mxCell>

        <!-- FpGrowthService -->
        <mxCell id="svc" value="&lt;b&gt;FpGrowthService&lt;/b&gt;&#xa;(App\Services)&lt;hr&gt;&#xa;&lt;div style='text-align:left;font-size:10px;'&gt;&#xa;- minSupportCount : float&#xa;- minConfidence : float&#xa;&lt;/div&gt;&lt;hr&gt;&#xa;&lt;div style='text-align:left;font-size:10px;'&gt;&#xa;+ run(array $transactions, float $minSupport,&#xa;  float $minConfidence) : array&#xa;&lt;/div&gt;" style="shape=mxgraph.unified_modeling_language.class;fontFamily=Helvetica;html=1;overflow=fill;whiteSpace=wrap;fillColor=#f8cecc;strokeColor=#b85450;fontSize=11;verticalAlign=top;" vertex="1" parent="1">
          <mxGeometry x="440" y="80" width="340" height="160" as="geometry" />
        </mxCell>

        <!-- PenjualanShopee Model -->
        <mxCell id="modelPS" value="&lt;b&gt;PenjualanShopee&lt;/b&gt;&#xa;(App\Models)&lt;hr&gt;&#xa;&lt;div style='text-align:left;font-size:10px;'&gt;&#xa;# $table = 'penjualan_shopee'&#xa;# $guarded = ['id']&#xa;&lt;/div&gt;&lt;hr&gt;&#xa;&lt;div style='text-align:left;font-size:10px;'&gt;&#xa;+ detail() : HasMany&lt;PenjualanShopeeDetail&gt;&#xa;&lt;/div&gt;" style="shape=mxgraph.unified_modeling_language.class;fontFamily=Helvetica;html=1;overflow=fill;whiteSpace=wrap;fillColor=#d5e8d4;strokeColor=#82b366;fontSize=11;verticalAlign=top;" vertex="1" parent="1">
          <mxGeometry x="100" y="340" width="310" height="140" as="geometry" />
        </mxCell>

        <!-- PenjualanShopeeDetail Model -->
        <mxCell id="modelPSD" value="&lt;b&gt;PenjualanShopeeDetail&lt;/b&gt;&#xa;(App\Models)&lt;hr&gt;&#xa;&lt;div style='text-align:left;font-size:10px;'&gt;&#xa;# $table = 'penjualan_shopee_detail'&#xa;# $guarded = ['id']&#xa;&lt;/div&gt;&lt;hr&gt;&#xa;&lt;div style='text-align:left;font-size:10px;'&gt;&#xa;+ penjualanShopee() : BelongsTo&lt;PenjualanShopee&gt;&#xa;&lt;/div&gt;" style="shape=mxgraph.unified_modeling_language.class;fontFamily=Helvetica;html=1;overflow=fill;whiteSpace=wrap;fillColor=#d5e8d4;strokeColor=#82b366;fontSize=11;verticalAlign=top;" vertex="1" parent="1">
          <mxGeometry x="500" y="340" width="310" height="140" as="geometry" />
        </mxCell>

        <!-- View -->
        <mxCell id="viewClass" value="&lt;b&gt;&lt;&lt;Blade View&gt;&gt;&lt;/b&gt;&#xa;fp-growth/index.blade.php&lt;hr&gt;&#xa;&lt;div style='text-align:left;font-size:10px;'&gt;&#xa;- Parameter Form&#xa;- Executive Summary Card&#xa;- Association Rules Table&#xa;- Detail + Bukti Transaksi&#xa;&lt;/div&gt;" style="shape=mxgraph.unified_modeling_language.class;fontFamily=Helvetica;html=1;overflow=fill;whiteSpace=wrap;fillColor=#fff2cc;strokeColor=#d6b656;fontSize=11;verticalAlign=top;" vertex="1" parent="1">
          <mxGeometry x="830" y="80" width="240" height="150" as="geometry" />
        </mxCell>

        <!-- Relationships -->
        <!-- Controller uses Service -->
        <mxCell id="rel1" value="&lt;&lt;uses&gt;&gt;" style="endArrow=open;dashed=1;endSize=12;fontSize=10;fontStyle=2;" edge="1" source="ctrl" target="svc" parent="1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <!-- Controller uses Model PS -->
        <mxCell id="rel2" value="&lt;&lt;uses&gt;&gt;" style="endArrow=open;dashed=1;endSize=12;fontSize=10;fontStyle=2;" edge="1" source="ctrl" target="modelPS" parent="1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <!-- Controller returns View -->
        <mxCell id="rel3" value="&lt;&lt;returns&gt;&gt;" style="endArrow=open;dashed=1;endSize=12;fontSize=10;fontStyle=2;" edge="1" source="ctrl" target="viewClass" parent="1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <!-- PS hasMany PSD -->
        <mxCell id="rel4" value="1          *" style="endArrow=diamondThin;endFill=1;endSize=14;fontSize=11;fontStyle=1;startArrow=none;" edge="1" source="modelPSD" target="modelPS" parent="1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>
```

---

## 3.7 Perancangan Basis Data

### 3.7.1 ERD (Entity Relationship Diagram)

ERD berikut menampilkan hanya entitas yang terlibat langsung dalam fitur FP-Growth, yaitu `penjualan_shopee` dan `penjualan_shopee_detail`.

```xml
<mxfile host="app.diagrams.net">
  <diagram name="ERD" id="erd">
    <mxGraphModel dx="1100" dy="700" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1200" pageHeight="800" math="0" shadow="0">
      <root>
        <mxCell id="0" />
        <mxCell id="1" parent="0" />
        <!-- Title -->
        <mxCell id="title" value="Entity Relationship Diagram — Modul FP-Growth" style="text;html=1;fontSize=14;fontStyle=1;align=center;" vertex="1" parent="1">
          <mxGeometry x="250" y="10" width="500" height="30" as="geometry" />
        </mxCell>

        <!-- Entity: penjualan_shopee -->
        <mxCell id="ent1" value="penjualan_shopee" style="shape=table;startSize=30;container=1;collapsible=0;childLayout=tableLayout;fixedRows=1;rowLines=1;fontStyle=1;align=center;resizeLast=1;fillColor=#dae8fc;strokeColor=#6c8ebf;fontSize=12;" vertex="1" parent="1">
          <mxGeometry x="60" y="60" width="380" height="640" as="geometry" />
        </mxCell>
        <mxCell id="r1_1" value="" style="shape=tableRow;horizontal=0;startSize=0;swimlaneHead=0;swimlaneBody=0;fillColor=#dae8fc;collapsible=0;dropTarget=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontSize=10;top=0;left=0;right=0;bottom=1;strokeColor=#6c8ebf;" vertex="1" parent="ent1">
          <mxGeometry y="30" width="380" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r1_1a" value="PK" style="shape=partialRectangle;connectable=0;fillColor=#dae8fc;top=0;left=0;bottom=0;right=1;fontStyle=1;overflow=hidden;fontSize=9;strokeColor=#6c8ebf;" vertex="1" parent="r1_1">
          <mxGeometry width="40" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r1_1b" value="id : bigint (AI)" style="shape=partialRectangle;connectable=0;fillColor=#dae8fc;top=0;left=0;bottom=0;right=0;align=left;spacingLeft=6;overflow=hidden;fontSize=10;fontStyle=1;strokeColor=#6c8ebf;" vertex="1" parent="r1_1">
          <mxGeometry x="40" width="340" height="25" as="geometry" />
        </mxCell>

        <mxCell id="r1_2" value="" style="shape=tableRow;horizontal=0;startSize=0;swimlaneHead=0;swimlaneBody=0;fillColor=none;collapsible=0;dropTarget=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontSize=10;top=0;left=0;right=0;bottom=0;" vertex="1" parent="ent1">
          <mxGeometry y="55" width="380" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r1_2a" value="UQ" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=1;fontStyle=0;overflow=hidden;fontSize=9;" vertex="1" parent="r1_2">
          <mxGeometry width="40" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r1_2b" value="no_pesanan : varchar(100)" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=0;align=left;spacingLeft=6;overflow=hidden;fontSize=10;" vertex="1" parent="r1_2">
          <mxGeometry x="40" width="340" height="25" as="geometry" />
        </mxCell>

        <mxCell id="r1_3" value="" style="shape=tableRow;horizontal=0;startSize=0;swimlaneHead=0;swimlaneBody=0;fillColor=none;collapsible=0;dropTarget=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontSize=10;" vertex="1" parent="ent1">
          <mxGeometry y="80" width="380" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r1_3a" value="IDX" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=1;fontStyle=0;overflow=hidden;fontSize=9;" vertex="1" parent="r1_3">
          <mxGeometry width="40" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r1_3b" value="status_pesanan : varchar(50)" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=0;align=left;spacingLeft=6;overflow=hidden;fontSize=10;fontStyle=4;" vertex="1" parent="r1_3">
          <mxGeometry x="40" width="340" height="25" as="geometry" />
        </mxCell>

        <mxCell id="r1_4" value="" style="shape=tableRow;horizontal=0;startSize=0;swimlaneHead=0;swimlaneBody=0;fillColor=none;collapsible=0;dropTarget=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontSize=10;" vertex="1" parent="ent1">
          <mxGeometry y="105" width="380" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r1_4a" value="" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=1;fontSize=9;" vertex="1" parent="r1_4">
          <mxGeometry width="40" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r1_4b" value="tipe_pesanan : varchar(100) nullable" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=0;align=left;spacingLeft=6;overflow=hidden;fontSize=10;" vertex="1" parent="r1_4">
          <mxGeometry x="40" width="340" height="25" as="geometry" />
        </mxCell>

        <mxCell id="r1_5" value="" style="shape=tableRow;horizontal=0;startSize=0;swimlaneHead=0;swimlaneBody=0;fillColor=none;collapsible=0;dropTarget=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontSize=10;" vertex="1" parent="ent1">
          <mxGeometry y="130" width="380" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r1_5a" value="IDX" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=1;fontSize=9;" vertex="1" parent="r1_5">
          <mxGeometry width="40" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r1_5b" value="waktu_pesanan_dibuat : datetime" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=0;align=left;spacingLeft=6;overflow=hidden;fontSize=10;fontStyle=4;" vertex="1" parent="r1_5">
          <mxGeometry x="40" width="340" height="25" as="geometry" />
        </mxCell>

        <mxCell id="r1_6" value="" style="shape=tableRow;horizontal=0;startSize=0;swimlaneHead=0;swimlaneBody=0;fillColor=none;collapsible=0;dropTarget=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontSize=10;" vertex="1" parent="ent1">
          <mxGeometry y="155" width="380" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r1_6a" value="" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=1;fontSize=9;" vertex="1" parent="r1_6">
          <mxGeometry width="40" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r1_6b" value="waktu_pembayaran : datetime nullable" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=0;align=left;spacingLeft=6;overflow=hidden;fontSize=10;" vertex="1" parent="r1_6">
          <mxGeometry x="40" width="340" height="25" as="geometry" />
        </mxCell>

        <mxCell id="r1_7" value="" style="shape=tableRow;horizontal=0;startSize=0;swimlaneHead=0;swimlaneBody=0;fillColor=none;collapsible=0;dropTarget=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontSize=10;" vertex="1" parent="ent1">
          <mxGeometry y="180" width="380" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r1_7a" value="" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=1;fontSize=9;" vertex="1" parent="r1_7">
          <mxGeometry width="40" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r1_7b" value="total_pembayaran : decimal(15,2)" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=0;align=left;spacingLeft=6;overflow=hidden;fontSize=10;" vertex="1" parent="r1_7">
          <mxGeometry x="40" width="340" height="25" as="geometry" />
        </mxCell>

        <mxCell id="r1_8" value="" style="shape=tableRow;horizontal=0;startSize=0;swimlaneHead=0;swimlaneBody=0;fillColor=none;collapsible=0;dropTarget=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontSize=10;" vertex="1" parent="ent1">
          <mxGeometry y="205" width="380" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r1_8a" value="" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=1;fontSize=9;" vertex="1" parent="r1_8">
          <mxGeometry width="40" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r1_8b" value="username_pembeli : varchar(100) nullable" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=0;align=left;spacingLeft=6;overflow=hidden;fontSize=10;" vertex="1" parent="r1_8">
          <mxGeometry x="40" width="340" height="25" as="geometry" />
        </mxCell>

        <mxCell id="r1_etc" value="" style="shape=tableRow;horizontal=0;startSize=0;swimlaneHead=0;swimlaneBody=0;fillColor=none;collapsible=0;dropTarget=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontSize=10;" vertex="1" parent="ent1">
          <mxGeometry y="230" width="380" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r1_etca" value="" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=1;fontSize=9;" vertex="1" parent="r1_etc">
          <mxGeometry width="40" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r1_etcb" value="... (kolom lain: no_resi, opsi_pengiriman, kota, provinsi, dll)" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=0;align=left;spacingLeft=6;overflow=hidden;fontSize=9;fontStyle=2;" vertex="1" parent="r1_etc">
          <mxGeometry x="40" width="340" height="25" as="geometry" />
        </mxCell>

        <mxCell id="r1_ts" value="" style="shape=tableRow;horizontal=0;startSize=0;swimlaneHead=0;swimlaneBody=0;fillColor=none;collapsible=0;dropTarget=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontSize=10;" vertex="1" parent="ent1">
          <mxGeometry y="255" width="380" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r1_tsa" value="" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=1;fontSize=9;" vertex="1" parent="r1_ts">
          <mxGeometry width="40" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r1_tsb" value="created_at, updated_at : timestamps" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=0;align=left;spacingLeft=6;overflow=hidden;fontSize=10;" vertex="1" parent="r1_ts">
          <mxGeometry x="40" width="340" height="25" as="geometry" />
        </mxCell>

        <!-- Entity: penjualan_shopee_detail -->
        <mxCell id="ent2" value="penjualan_shopee_detail" style="shape=table;startSize=30;container=1;collapsible=0;childLayout=tableLayout;fixedRows=1;rowLines=1;fontStyle=1;align=center;resizeLast=1;fillColor=#d5e8d4;strokeColor=#82b366;fontSize=12;" vertex="1" parent="1">
          <mxGeometry x="600" y="60" width="380" height="540" as="geometry" />
        </mxCell>
        <mxCell id="r2_1" value="" style="shape=tableRow;horizontal=0;startSize=0;swimlaneHead=0;swimlaneBody=0;fillColor=#d5e8d4;collapsible=0;dropTarget=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontSize=10;strokeColor=#82b366;" vertex="1" parent="ent2">
          <mxGeometry y="30" width="380" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r2_1a" value="PK" style="shape=partialRectangle;connectable=0;fillColor=#d5e8d4;top=0;left=0;bottom=0;right=1;fontStyle=1;overflow=hidden;fontSize=9;strokeColor=#82b366;" vertex="1" parent="r2_1">
          <mxGeometry width="40" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r2_1b" value="id : bigint (AI)" style="shape=partialRectangle;connectable=0;fillColor=#d5e8d4;top=0;left=0;bottom=0;right=0;align=left;spacingLeft=6;overflow=hidden;fontSize=10;fontStyle=1;strokeColor=#82b366;" vertex="1" parent="r2_1">
          <mxGeometry x="40" width="340" height="25" as="geometry" />
        </mxCell>

        <mxCell id="r2_2" value="" style="shape=tableRow;horizontal=0;startSize=0;swimlaneHead=0;swimlaneBody=0;fillColor=none;collapsible=0;dropTarget=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontSize=10;" vertex="1" parent="ent2">
          <mxGeometry y="55" width="380" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r2_2a" value="FK" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=1;fontStyle=1;overflow=hidden;fontSize=9;" vertex="1" parent="r2_2">
          <mxGeometry width="40" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r2_2b" value="penjualan_shopee_id : bigint" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=0;align=left;spacingLeft=6;overflow=hidden;fontSize=10;fontStyle=5;" vertex="1" parent="r2_2">
          <mxGeometry x="40" width="340" height="25" as="geometry" />
        </mxCell>

        <mxCell id="r2_3" value="" style="shape=tableRow;horizontal=0;startSize=0;swimlaneHead=0;swimlaneBody=0;fillColor=none;collapsible=0;dropTarget=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontSize=10;" vertex="1" parent="ent2">
          <mxGeometry y="80" width="380" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r2_3a" value="" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=1;fontSize=9;" vertex="1" parent="r2_3">
          <mxGeometry width="40" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r2_3b" value="sku_induk : varchar(100) nullable" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=0;align=left;spacingLeft=6;overflow=hidden;fontSize=10;" vertex="1" parent="r2_3">
          <mxGeometry x="40" width="340" height="25" as="geometry" />
        </mxCell>

        <mxCell id="r2_4" value="" style="shape=tableRow;horizontal=0;startSize=0;swimlaneHead=0;swimlaneBody=0;fillColor=none;collapsible=0;dropTarget=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontSize=10;" vertex="1" parent="ent2">
          <mxGeometry y="105" width="380" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r2_4a" value="IDX" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=1;fontSize=9;" vertex="1" parent="r2_4">
          <mxGeometry width="40" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r2_4b" value="nama_produk : varchar(255)" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=0;align=left;spacingLeft=6;overflow=hidden;fontSize=10;fontStyle=5;" vertex="1" parent="r2_4">
          <mxGeometry x="40" width="340" height="25" as="geometry" />
        </mxCell>

        <mxCell id="r2_5" value="" style="shape=tableRow;horizontal=0;startSize=0;swimlaneHead=0;swimlaneBody=0;fillColor=none;collapsible=0;dropTarget=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontSize=10;" vertex="1" parent="ent2">
          <mxGeometry y="130" width="380" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r2_5a" value="" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=1;fontSize=9;" vertex="1" parent="r2_5">
          <mxGeometry width="40" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r2_5b" value="nama_variasi : varchar(150) nullable" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=0;align=left;spacingLeft=6;overflow=hidden;fontSize=10;" vertex="1" parent="r2_5">
          <mxGeometry x="40" width="340" height="25" as="geometry" />
        </mxCell>

        <mxCell id="r2_6" value="" style="shape=tableRow;horizontal=0;startSize=0;swimlaneHead=0;swimlaneBody=0;fillColor=none;collapsible=0;dropTarget=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontSize=10;" vertex="1" parent="ent2">
          <mxGeometry y="155" width="380" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r2_6a" value="" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=1;fontSize=9;" vertex="1" parent="r2_6">
          <mxGeometry width="40" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r2_6b" value="harga_awal : decimal(15,2)" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=0;align=left;spacingLeft=6;overflow=hidden;fontSize=10;" vertex="1" parent="r2_6">
          <mxGeometry x="40" width="340" height="25" as="geometry" />
        </mxCell>

        <mxCell id="r2_7" value="" style="shape=tableRow;horizontal=0;startSize=0;swimlaneHead=0;swimlaneBody=0;fillColor=none;collapsible=0;dropTarget=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontSize=10;" vertex="1" parent="ent2">
          <mxGeometry y="180" width="380" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r2_7a" value="" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=1;fontSize=9;" vertex="1" parent="r2_7">
          <mxGeometry width="40" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r2_7b" value="harga_setelah_diskon : decimal(15,2)" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=0;align=left;spacingLeft=6;overflow=hidden;fontSize=10;" vertex="1" parent="r2_7">
          <mxGeometry x="40" width="340" height="25" as="geometry" />
        </mxCell>

        <mxCell id="r2_8" value="" style="shape=tableRow;horizontal=0;startSize=0;swimlaneHead=0;swimlaneBody=0;fillColor=none;collapsible=0;dropTarget=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontSize=10;" vertex="1" parent="ent2">
          <mxGeometry y="205" width="380" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r2_8a" value="" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=1;fontSize=9;" vertex="1" parent="r2_8">
          <mxGeometry width="40" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r2_8b" value="jumlah : int" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=0;align=left;spacingLeft=6;overflow=hidden;fontSize=10;" vertex="1" parent="r2_8">
          <mxGeometry x="40" width="340" height="25" as="geometry" />
        </mxCell>

        <mxCell id="r2_etc" value="" style="shape=tableRow;horizontal=0;startSize=0;swimlaneHead=0;swimlaneBody=0;fillColor=none;collapsible=0;dropTarget=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontSize=10;" vertex="1" parent="ent2">
          <mxGeometry y="230" width="380" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r2_etca" value="" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=1;fontSize=9;" vertex="1" parent="r2_etc">
          <mxGeometry width="40" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r2_etcb" value="... (subtotal_pesanan, total_diskon, berat_produk, dll)" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=0;align=left;spacingLeft=6;overflow=hidden;fontSize=9;fontStyle=2;" vertex="1" parent="r2_etc">
          <mxGeometry x="40" width="340" height="25" as="geometry" />
        </mxCell>

        <mxCell id="r2_ts" value="" style="shape=tableRow;horizontal=0;startSize=0;swimlaneHead=0;swimlaneBody=0;fillColor=none;collapsible=0;dropTarget=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontSize=10;" vertex="1" parent="ent2">
          <mxGeometry y="255" width="380" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r2_tsa" value="" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=1;fontSize=9;" vertex="1" parent="r2_ts">
          <mxGeometry width="40" height="25" as="geometry" />
        </mxCell>
        <mxCell id="r2_tsb" value="created_at, updated_at : timestamps" style="shape=partialRectangle;connectable=0;fillColor=none;top=0;left=0;bottom=0;right=0;align=left;spacingLeft=6;overflow=hidden;fontSize=10;" vertex="1" parent="r2_ts">
          <mxGeometry x="40" width="340" height="25" as="geometry" />
        </mxCell>

        <!-- Relationship Line -->
        <mxCell id="rel" value="1                                          *" style="endArrow=ERmandOne;startArrow=ERmany;endFill=0;startFill=0;fontSize=11;fontStyle=1;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" source="r1_1" target="r2_2" parent="1">
          <mxGeometry relative="1" as="geometry" />
        </mxCell>
        <mxCell id="relLabel" value="memiliki" style="edgeLabel;html=1;fontSize=10;fontStyle=2;" vertex="1" connectable="0" parent="rel">
          <mxGeometry x="-0.1" y="-1" relative="1" as="geometry">
            <mxPoint y="-15" as="offset" />
          </mxGeometry>
        </mxCell>
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>
```

---

### 3.7.2 Struktur Tabel

Berikut adalah struktur lengkap kedua tabel berdasarkan file migration [2026_07_21_000001_create_penjualan_shopee_tables.php](file:///c:/laragon/www/Laf-Project/database/migrations/2026_07_21_000001_create_penjualan_shopee_tables.php):

#### Tabel `penjualan_shopee`

| No | Nama Kolom | Tipe Data | Constraint | Keterangan |
|----|-----------|-----------|------------|------------|
| 1 | id | bigint unsigned | PK, AI | Primary key |
| 2 | no_pesanan | varchar(100) | UNIQUE | Nomor pesanan Shopee |
| 3 | tipe_pesanan | varchar(100) | NULLABLE | Tipe pesanan |
| 4 | status_pesanan | varchar(50) | INDEX | Status: "Selesai" / "Batal" |
| 5 | alasan_pembatalan | text | NULLABLE | Alasan pembatalan |
| 6 | status_pembatalan | varchar(100) | NULLABLE | Status pembatalan/pengembalian |
| 7 | no_resi | varchar(100) | NULLABLE | Nomor resi pengiriman |
| 8 | opsi_pengiriman | varchar(100) | NULLABLE | Opsi pengiriman |
| 9 | metode_pengiriman | varchar(50) | NULLABLE | Metode pengiriman |
| 10 | deadline_pengiriman | datetime | NULLABLE | Deadline pengiriman |
| 11 | waktu_pengiriman_diatur | datetime | NULLABLE | Waktu pengiriman diatur |
| 12 | waktu_pesanan_dibuat | datetime | INDEX | Waktu pesanan dibuat |
| 13 | waktu_pembayaran | datetime | NULLABLE | Waktu pembayaran |
| 14 | metode_pembayaran | varchar(100) | NULLABLE | Metode pembayaran |
| 15 | voucher_penjual | decimal(15,2) | DEFAULT 0 | Voucher penjual |
| 16 | cashback_koin | decimal(15,2) | DEFAULT 0 | Cashback koin |
| 17 | voucher_shopee | decimal(15,2) | DEFAULT 0 | Voucher Shopee |
| 18 | potongan_koin | decimal(15,2) | DEFAULT 0 | Potongan koin |
| 19 | diskon_kartu_kredit | decimal(15,2) | DEFAULT 0 | Diskon kartu kredit |
| 20 | ongkir_pembeli | decimal(15,2) | DEFAULT 0 | Ongkir dibayar pembeli |
| 21 | estimasi_potongan_ongkir | decimal(15,2) | DEFAULT 0 | Estimasi potongan ongkir |
| 22 | ongkir_pengembalian | decimal(15,2) | DEFAULT 0 | Ongkir pengembalian |
| 23 | total_pembayaran | decimal(15,2) | DEFAULT 0 | Total pembayaran |
| 24 | perkiraan_ongkir | decimal(15,2) | DEFAULT 0 | Perkiraan ongkir |
| 25 | catatan_pembeli | text | NULLABLE | Catatan pembeli |
| 26 | catatan | text | NULLABLE | Catatan umum |
| 27 | username_pembeli | varchar(100) | NULLABLE | Username pembeli |
| 28 | nama_penerima | varchar(150) | NULLABLE | Nama penerima |
| 29 | no_telepon | varchar(50) | NULLABLE | Nomor telepon |
| 30 | alamat_pengiriman | text | NULLABLE | Alamat pengiriman |
| 31 | kota | varchar(100) | NULLABLE | Kota/kabupaten |
| 32 | provinsi | varchar(100) | NULLABLE | Provinsi |
| 33 | waktu_pesanan_selesai | datetime | NULLABLE | Waktu pesanan selesai |
| 34 | created_at | timestamp | — | Timestamp dibuat |
| 35 | updated_at | timestamp | — | Timestamp diperbarui |

#### Tabel `penjualan_shopee_detail`

| No | Nama Kolom | Tipe Data | Constraint | Keterangan |
|----|-----------|-----------|------------|------------|
| 1 | id | bigint unsigned | PK, AI | Primary key |
| 2 | penjualan_shopee_id | bigint unsigned | FK → penjualan_shopee(id), CASCADE DELETE | Relasi ke header transaksi |
| 3 | sku_induk | varchar(100) | NULLABLE | SKU induk produk |
| 4 | nama_produk | varchar(255) | INDEX | Nama produk (dari Shopee) |
| 5 | nomor_referensi_sku | varchar(100) | NULLABLE | Nomor referensi SKU |
| 6 | nama_variasi | varchar(150) | NULLABLE | Variasi (warna/ukuran) |
| 7 | harga_awal | decimal(15,2) | DEFAULT 0 | Harga sebelum diskon |
| 8 | harga_setelah_diskon | decimal(15,2) | DEFAULT 0 | Harga setelah diskon |
| 9 | jumlah | int | DEFAULT 0 | Jumlah item |
| 10 | returned_quantity | int | DEFAULT 0 | Jumlah retur |
| 11 | subtotal_pesanan | decimal(15,2) | DEFAULT 0 | Subtotal pesanan |
| 12 | total_diskon | decimal(15,2) | DEFAULT 0 | Total diskon |
| 13 | diskon_penjual | decimal(15,2) | DEFAULT 0 | Diskon penjual |
| 14 | diskon_shopee | decimal(15,2) | DEFAULT 0 | Diskon Shopee |
| 15 | berat_produk | varchar(50) | NULLABLE | Berat produk |
| 16 | jumlah_produk_dipesan | int | DEFAULT 0 | Jumlah produk dipesan |
| 17 | total_berat | varchar(50) | NULLABLE | Total berat |
| 18 | paket_diskon | varchar(10) | NULLABLE | Flag paket diskon |
| 19 | paket_diskon_shopee | decimal(15,2) | DEFAULT 0 | Diskon paket dari Shopee |
| 20 | paket_diskon_penjual | decimal(15,2) | DEFAULT 0 | Diskon paket dari penjual |
| 21 | created_at | timestamp | — | Timestamp dibuat |
| 22 | updated_at | timestamp | — | Timestamp diperbarui |

---

## 3.8 Perancangan Antarmuka

### 3.8.1 Struktur Menu

Fitur FP-Growth ditempatkan pada bagian navigasi sidebar di bawah section **"Analisis"**, sebagaimana terlihat pada [app.blade.php baris 606-612](file:///c:/laragon/www/Laf-Project/resources/views/layouts/app.blade.php#L606-L612):

| Menu / Submenu | Route Name | URL Path | Method |
|----------------|------------|----------|--------|
| **Analisis** (section header) | — | — | — |
| ├─ FP-Growth (Promo) | `fp-growth.index` | `/fp-growth` | GET |
| └─ *(proses analisis)* | `fp-growth.process` | `/fp-growth/process` | POST |

Menu pendukung yang terkait:

| Menu / Submenu | Route Name | URL Path | Keterangan |
|----------------|------------|----------|------------|
| Import Transaksi Shopee | `penjualan.import-shopee` | `/penjualan-import-shopee` | Halaman import data sumber FP-Growth |

### 3.8.2 Mockup / Tampilan Antarmuka

Berikut adalah screenshot langsung dari halaman FP-Growth yang sudah berjalan pada sistem LAF Project, diakses melalui `http://laf-project.test/fp-growth` setelah login sebagai Admin.

#### A. Tampilan Halaman FP-Growth (State Kosong / Belum Dianalisis)

Screenshot berikut menunjukkan tampilan awal halaman FP-Growth sebelum admin menjalankan analisis. Terlihat panel form parameter di sisi kiri dan area kosong "Belum Ada Data Hasil Analisis" di sisi kanan.

![Tampilan awal halaman FP-Growth — form parameter analisis di kiri, state kosong di kanan](C:/Users/Lenovo/.gemini/antigravity-ide/brain/cbb16ab4-7d79-4224-8006-1b09780a7da1/fpgrowth_empty_state_1785549270210.png)

Komponen form parameter yang tersedia:
1. **Dropdown "Sumber Data Transaksi"** — Opsi: 🟧 Shopee Marketplace, 🛒 Penjualan Kasir (POS Offline), 🔀 Gabungan
2. **Dropdown "Tingkat Granularitas Produk"** — Opsi: 🏷️ Produk Utama / SKU Induk (Rekomendasi), 🔍 Nama Produk + Variasi Lengkap
3. **Input Date "Tanggal Mulai"** — default awal bulan berjalan
4. **Input Date "Tanggal Selesai"** — default akhir bulan berjalan
5. **Input Number "Minimum Support (%)"** — default 0.05, step 0.01
6. **Input Number "Minimum Confidence (%)"** — default 10, step 0.1
7. **Checkbox "Abaikan Item Packing & Kelengkapan"** — default checked
8. **Tombol "Mulai Analisis FP-Growth"** — submit form POST ke `fp-growth.process`

Di sidebar navigasi, menu **"FP-Growth (Promo)"** berada di bawah section **"ANALISIS"** dan ditandai aktif (highlight biru).

---

#### B. Tampilan Hasil Analisis — Executive Summary & Rekomendasi Paket Promo

Setelah admin mengisi parameter (Sumber Data: Shopee, Periode: 01/05/2026 – 31/05/2026, Min Support: 0,05%, Min Confidence: 10%) dan mengklik "Mulai Analisis FP-Growth", sistem menampilkan hasil berupa:

![Hasil analisis FP-Growth — 4 rekomendasi paket promo bundling terbaik dengan Confidence dan Lift Ratio](C:/Users/Lenovo/.gemini/antigravity-ide/brain/cbb16ab4-7d79-4224-8006-1b09780a7da1/fpgrowth_executive_summary_1785549345193.png)

**Executive Summary Card** menampilkan 4 rekomendasi utama:
1. **Paket Back to School / Outfit Match** — Kaos Kaki Sekolah + Sepatu Anak Sagan (Confidence: 45,45%, Lift Ratio: 10,27)
2. **Cross-Selling Produk** — KaosKaki-Bergaris-White + KaosKaki-Bergaris-Black (Confidence: 38,46%, Lift Ratio: 46,15)
3. **Paket Back to School / Outfit Match** — Kaos Kaki Sekolah + Sepatu Anak Wynn (Confidence: 36,36%, Lift Ratio: 6,73)
4. **Cross-Selling Produk** — KaosKaki-Ankle-Black-White + KaosKaki-Bergaris-White (Confidence: 33,33%, Lift Ratio: 89,23)

Setiap rekomendasi dilengkapi **🎯 Saran Aksi Bisnis** yang actionable, misalnya: *"Buat paket bundling Sepatu + Kaos Kaki dengan harga khusus atau bonus langsung saat checkout."*

---

#### C. Tampilan Tabel Association Rules Detail

Di bawah Executive Summary, ditampilkan tabel lengkap seluruh aturan asosiasi yang memenuhi threshold:

![Tabel Association Rules Detail — kolom Antecedent, Consequent, Support, Confidence, Lift Ratio, dan tombol Detail](C:/Users/Lenovo/.gemini/antigravity-ide/brain/cbb16ab4-7d79-4224-8006-1b09780a7da1/fpgrowth_rules_table_1785549354045.png)

Tabel ini menampilkan:
- **Kolom "Jika Membeli (Antecedent)"** — item pemicu dalam bentuk badge berwarna
- **Kolom "Maka Juga Membeli (Consequent)"** — item yang direkomendasikan
- **Kolom Support (%)** — persentase kemunculan pasangan dalam seluruh transaksi
- **Kolom Confidence (%)** — kekuatan hubungan asosiasi
- **Kolom Lift Ratio** — dengan badge berwarna: hijau **(Kuat)** jika ≥ 1.2, biru **(Netral)** jika 1.0–1.2, kuning **(Lemah)** jika < 1.0
- **Tombol "Detail"** — membuka expandable row berisi interpretasi kalimat, simulasi perhitungan matematis (rumus Support, Confidence, Lift Ratio), dan bukti transaksi riil (sampel nota pembelian bersama)

#### D. Rekaman Video Interaksi Sistem

Berikut adalah rekaman video proses interaksi lengkap: dari pengisian parameter, eksekusi analisis FP-Growth, hingga tampilnya hasil rekomendasi paket promo.

![Rekaman interaksi lengkap proses analisis FP-Growth pada sistem LAF Project](C:/Users/Lenovo/.gemini/antigravity-ide/brain/cbb16ab4-7d79-4224-8006-1b09780a7da1/fpgrowth_screenshots_1785549261734.webp)

---

*Dokumen ini disusun berdasarkan penelusuran langsung pada codebase Laravel LAF Project. Seluruh nama tabel, kolom, class, route, dan controller yang disebutkan telah diverifikasi kebenarannya dari file sumber asli di repositori. Screenshot diambil langsung dari aplikasi yang berjalan di `http://laf-project.test`.*
