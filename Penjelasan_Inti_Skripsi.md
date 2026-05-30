# PANDUAN INTI PENELITIAN SKRIPSI
**Judul:** Penerapan Algoritma FP-Growth untuk Rekomendasi Paket Promo Produk pada Sistem Informasi Point of Sales (Studi Kasus: Laf Project)

---

## 1. Analisis Masalah Mendalam (The Problem)

### A. Fenomena "Sampah Data" (Data Garbage)
Setiap transaksi di kasir (POS) Laf Project menghasilkan data detail berupa item yang dibeli, jumlah, dan waktu transaksi. Masalahnya, data ini hanya dianggap sebagai "bukti bayar" dan laporan keuangan semata. Dalam dunia IT, ini disebut sebagai *Data Rich but Information Poor* (Kaya Data tapi Miskin Informasi). Data menumpuk di database tanpa pernah diekstraksi maknanya.

### B. Subjektivitas Pengambilan Keputusan
Selama ini, manajemen Laf Project menentukan strategi promo (seperti paket bundling) hanya berdasarkan:
*   **Intuisi/Perasaan:** "Kayaknya barang ini cocok dipaketkan."
*   **Pengamatan Sekilas:** Tanpa perhitungan persentase yang valid.
*   **Trial and Error:** Membuat promo lalu melihat apakah laku atau tidak. Jika tidak laku, berarti rugi biaya cetak promo dan waktu.

---

## 2. Dampak Negatif (The Negative Impact)

Jika masalah di atas tidak diselesaikan, Laf Project akan menghadapi risiko:
1.  **Inefisiensi Pemasaran:** Paket promo yang ditawarkan tidak relevan dengan kebutuhan nyata pelanggan. Contoh: Memaketkan Sepatu dengan Topi, padahal secara data, orang yang beli sepatu lebih butuh Kaos Kaki.
2.  **Stagnasi Stok (Dead Stock):** Barang-barang yang seharusnya bisa terjual cepat melalui promo paket tetap mengendap di gudang karena kombinasi paketnya salah.
3.  **Kehilangan Keunggulan Kompetitif:** Di era ritel modern, pesaing sudah menggunakan analisis data. Tanpa teknologi ini, Laf Project akan tertinggal dalam memahami perilaku pelanggannya sendiri.

---

## 3. Solusi & Tujuan Penelitian (The Goal)

### A. Transformasi POS Menjadi Sistem Cerdas
Penelitian ini bertujuan mengubah sistem POS Laf Project dari sekadar "Alat Catat" menjadi "Alat Analisis". Kita menyuntikkan algoritma **FP-Growth** ke dalam sistem.

### B. Kenapa Memilih FP-Growth? (Alasan Ilmiah)
Ini poin penting untuk menjawab pertanyaan dosen:
*   **Efisien:** FP-Growth tidak membebani server karena hanya melakukan dua kali scan database.
*   **Tanpa Kandidat:** Berbeda dengan algoritma Apriori, FP-Growth menggunakan struktur *FP-Tree* yang tidak perlu membuat ribuan kombinasi sementara di memori, sehingga jauh lebih cepat untuk data transaksi yang besar.

---

## 4. Dampak Positif Setelah Implementasi (The Future Impact)

### A. Strategi Bisnis yang Terukur (Data-Driven)
Manajemen kini memiliki bukti ilmiah (berupa nilai **Support** dan **Confidence**) untuk setiap paket promo yang akan dibuat. Keputusan tidak lagi berdasarkan "katanya", tapi berdasarkan "datanya".

### B. Personalisasi Promosi
Sistem dapat menemukan pola yang mungkin tidak terpikirkan oleh manusia. Misalnya, ternyata orang yang beli *Laf Urban Flip* selalu beli *Tali Sandal Gunung*. Dengan informasi ini, toko bisa meningkatkan penjualan item kecil yang sering terlupakan.

### C. Efisiensi Manajemen Stok
Dengan mengetahui barang apa yang sering dibeli bersamaan, bagian gudang dapat mengatur tata letak barang agar berdekatan, atau bagian marketing bisa menghabiskan stok barang tertentu dengan memaketkannya dengan barang yang sangat laku (Top Seller).

---

## 5. Ringkasan Narasi untuk Sidang (Elevator Pitch)

"Penelitian saya dilatarbelakangi oleh banyaknya data transaksi di Laf Project yang tidak dimanfaatkan untuk strategi pemasaran. Selama ini promo dibuat secara manual dan subjektif, sehingga sering tidak tepat sasaran. 

Melalui skripsi ini, saya mengimplementasikan algoritma FP-Growth untuk melakukan *Market Basket Analysis*. Sistem akan secara otomatis mengolah data transaksi tersebut menjadi **Rekomendasi Paket Promo** yang valid secara matematis. 

Hasil akhirnya, Laf Project dapat meningkatkan omzet dan efisiensi melalui strategi *Cross-Selling* yang akurat, mengubah tumpukan data transaksi menjadi keuntungan bisnis yang nyata."
