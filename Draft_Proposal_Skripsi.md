# DRAFT PROPOSAL SKRIPSI

**Judul:** Penerapan Algoritma FP-Growth untuk Rekomendasi Paket Promo Produk pada Sistem Informasi *Point of Sales* (Studi Kasus: Laf Project)

---

## 1.1 Latar Belakang

Sistem Informasi *Point of Sales* (POS) saat ini tidak hanya berfungsi sebagai mesin kasir elektronik pencatat transaksi jual-beli, tetapi juga telah berevolusi menjadi tambang data (*data warehouse*) yang sangat berharga bagi perusahaan. Setiap harinya, Laf Project menghasilkan puluhan hingga ratusan data struk transaksi. Namun, berdasarkan observasi yang dilakukan, kumpulan data transaksi historis tersebut saat ini hanya dibiarkan menumpuk sebagai laporan keuangan (arsip), tanpa adanya upaya ekstraksi informasi (*Data Mining*) untuk mendukung pengambilan keputusan strategis bisnis.

Permasalahan utama yang dihadapi oleh manajemen Laf Project saat ini adalah kesulitan dalam menentukan strategi pemasaran yang tepat, khususnya dalam pembuatan paket promo produk (*bundling*). Penentuan paket promo selama ini masih dilakukan berdasarkan insting atau tebakan (*trial and error*), yang seringkali mengakibatkan promo kurang diminati konsumen karena kombinasi barang yang ditawarkan tidak sesuai dengan kebiasaan belanja pelanggan. 

Untuk memecahkan masalah ini, diperlukan implementasi teknologi *Data Mining* menggunakan teknik *Market Basket Analysis* (Analisis Keranjang Belanja). Metode ini bertujuan untuk menemukan aturan asosiasi (hubungan) antar item produk yang sering dibeli secara bersamaan oleh pelanggan. Dalam penelitian ini, algoritma yang digunakan adalah Algoritma *Frequent Pattern Growth* (FP-Growth). Algoritma FP-Growth dipilih karena memiliki performa dan kecepatan komputasi yang jauh lebih efisien dibandingkan algoritma pendahulunya (seperti Apriori), karena FP-Growth menggunakan struktur struktur *FP-Tree* sehingga tidak memerlukan *generate candidate* yang memakan banyak memori server.

Dengan mengintegrasikan algoritma FP-Growth ke dalam Sistem Informasi POS berbasis web yang sedang dikembangkan, sistem diharapkan mampu memberikan "kecerdasan buatan" sederhana. Sistem akan menganalisis riwayat transaksi secara otomatis dan memberikan rekomendasi cerdas kepada pemilik toko mengenai kombinasi produk yang paling menguntungkan untuk dijadikan paket promo silang (*Cross-Selling*). 

Berdasarkan latar belakang tersebut, penulis bermaksud melakukan penelitian dengan judul **"Penerapan Algoritma FP-Growth untuk Rekomendasi Paket Promo Produk pada Sistem Informasi *Point of Sales* (Studi Kasus: Laf Project)"**.

## 1.2 Identifikasi Masalah

Berdasarkan latar belakang yang telah diuraikan, identifikasi masalah dalam penelitian ini adalah:
1. Besarnya volume data riwayat transaksi penjualan di Laf Project yang belum dimanfaatkan secara optimal untuk analisis bisnis.
2. Seringnya terjadi kesalahan dalam penentuan paket promo (*bundling*) karena manajemen tidak memiliki data objektif mengenai pola belanja konsumen.
3. Belum adanya fitur cerdas (*Smart Feature*) pada sistem POS saat ini yang dapat memberikan rekomendasi otomatis berdasarkan *Data Mining*.

## 1.3 Batasan Masalah

Agar penelitian ini lebih terarah dan fokus, maka diberikan batasan masalah sebagai berikut:
1. Algoritma *Data Mining* yang digunakan dibatasi hanya pada metode pencarian aturan asosiasi menggunakan Algoritma FP-Growth.
2. Dataset yang diolah merupakan data riwayat transaksi kasir (struk) dari *database* Laf Project pada periode tertentu.
3. *Output* atau hasil akhir dari algoritma ini adalah nilai *Support* dan *Confidence* yang menampilkan aturan asosiasi antar barang (Rekomendasi Promo).
4. Sistem informasi dibangun menggunakan *framework* berbasis web (Laravel) dan tidak mencakup modul prediksi (*forecasting*) masa depan.

## 1.4 Rumusan Masalah

Berdasarkan identifikasi masalah, rumusan masalah dalam penelitian ini adalah:
1. Bagaimana mengimplementasikan Algoritma FP-Growth untuk menemukan pola asosiasi dari kumpulan data transaksi penjualan di Laf Project?
2. Bagaimana membangun Sistem Informasi *Point of Sales* (POS) Berbasis Web yang terintegrasi dengan modul *Data Mining* FP-Growth untuk menampilkan rekomendasi paket promo?

## 1.5 Tujuan dan Kegunaan Penelitian

### 1.5.1 Tujuan Penelitian
1. Menemukan pola kebiasaan pembelian pelanggan melalui implementasi Algoritma FP-Growth pada data historis Laf Project.
2. Mengembangkan Sistem Informasi POS cerdas yang mampu menyajikan informasi aturan asosiasi guna membantu manajemen mengambil keputusan strategi *Cross-Selling*.

### 1.5.2 Kegunaan Penelitian
1. **Bagi Akademis:** Memberikan kontribusi pada literatur Ilmu Komputer, khususnya mengenai penerapan *Data Mining* (Algoritma FP-Growth) dalam sistem kasir berbasis *web*.
2. **Bagi Laf Project:** Memberikan alat (*tools*) analisis otomatis yang dapat meningkatkan omzet perusahaan melalui strategi promosi paket yang lebih akurat dan *data-driven*.

## 1.6 Objek dan Waktu Penelitian

### 1.6.1 Objek Penelitian
Objek penelitian difokuskan pada pengolahan data riwayat transaksi penjualan (Tabel `penjualan` dan `detail_penjualan`) pada sistem POS Laf Project.

### 1.6.2 Waktu Penelitian
Penelitian ini direncanakan memakan waktu selama 4 (empat) bulan, mulai dari Mei 2026 hingga Agustus 2026.

## 1.7 Landasan Teori

Bagian ini akan memuat landasan konseptual, di antaranya:
1. **Sistem Informasi Point of Sales:** Teori tentang sistem kasir dan pencatatan transaksi ritel.
2. **Data Mining & Knowledge Discovery in Database (KDD):** Penggalian informasi berharga dari kumpulan data besar.
3. **Market Basket Analysis:** Teori tentang analisis kebiasaan belanja (*Cross-Selling*).
4. **Algoritma FP-Growth:** Penjelasan matematis mengenai *Support*, *Confidence*, pembuatan *FP-Tree*, dan cara kerja *conditional pattern base*.
5. **Web Development:** Teori mengenai Framework Laravel dan arsitektur *database* relasional.

## 1.8 Metodologi Penelitian

### 1.8.1 Metode Penelitian (Pengumpulan Data)
1. **Wawancara:** Melakukan sesi tanya jawab dengan pihak manajemen/marketing Laf Project mengenai kendala pembuatan promo saat ini.
2. **Observasi & Pengumpulan Dataset:** Mengambil sampel (*dump*) data transaksi dari *database* untuk dijadikan bahan pengujian (*training*) algoritma.
3. **Studi Pustaka:** Mengkaji jurnal-jurnal ilmiah terbaru mengenai komparasi algoritma Apriori dan FP-Growth.

### 1.8.2 Metode Pengembangan Sistem
Penelitian ini menggunakan metode pengembangan perangkat lunak berbasis *Waterfall* (atau *Prototyping*), yang meliputi tahapan:
1. **Analisis Kebutuhan Sistem:** Mengidentifikasi alur kerja POS dan kebutuhan sistem *Data Mining*.
2. **Desain Sistem:** Pembuatan ERD (*Entity Relationship Diagram*) untuk menampung *rules* FP-Growth, serta desain antarmuka (*UI/UX*) modul analisis.
3. **Implementasi Kode:** Penulisan *source code* Algoritma FP-Growth dalam bahasa PHP serta pembuatan fitur kasir dengan framework Laravel.
4. **Pengujian (Testing):** Menggunakan *Black Box Testing* untuk fitur web, dan membandingkan akurasi hasil perhitungan FP-Growth antara sistem buatan sendiri dengan *software Data Mining* standar (seperti RapidMiner atau Weka).
5. **Pemeliharaan:** Serah terima (*deployment*) sistem kepada pengguna (Laf Project).
