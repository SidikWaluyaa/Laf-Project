<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\PenjualanShopee;
use App\Models\PenjualanShopeeDetail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShopeeImportService
{
    public function import(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (count($rows) <= 1) {
            throw new \Exception('File Excel kosong atau tidak memiliki data.');
        }

        $headers = array_map(function($h) {
            return trim((string)$h);
        }, $rows[0]);

        // Map column indices
        $colIndex = [];
        foreach ($headers as $idx => $headerName) {
            $colIndex[$headerName] = $idx;
        }

        $requiredHeader = 'No. Pesanan';
        if (!isset($colIndex[$requiredHeader])) {
            throw new \Exception('Format Excel Shopee tidak valid. Kolom "No. Pesanan" tidak ditemukan.');
        }

        // Group rows by No. Pesanan
        $groupedOrders = [];
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $noPesanan = trim((string)($row[$colIndex['No. Pesanan']] ?? ''));
            if (empty($noPesanan)) {
                continue;
            }
            $groupedOrders[$noPesanan][] = $row;
        }

        $totalOrdersImported = 0;
        $totalDetailsImported = 0;
        $totalSkippedOrders = 0;

        DB::transaction(function () use ($groupedOrders, $colIndex, &$totalOrdersImported, &$totalDetailsImported, &$totalSkippedOrders) {
            foreach ($groupedOrders as $noPesanan => $orderRows) {
                $firstRow = $orderRows[0];

                $statusPesanan = trim((string)($this->getVal($firstRow, $colIndex, 'Status Pesanan')));
                $waktuPesananDibuat = $this->parseDate($this->getVal($firstRow, $colIndex, 'Waktu Pesanan Dibuat'));

                if (!$waktuPesananDibuat) {
                    $waktuPesananDibuat = now()->format('Y-m-d H:i:s');
                }

                $headerData = [
                    'no_pesanan' => $noPesanan,
                    'tipe_pesanan' => $this->getVal($firstRow, $colIndex, 'Tipe Pesanan'),
                    'status_pesanan' => $statusPesanan,
                    'alasan_pembatalan' => $this->getVal($firstRow, $colIndex, 'Alasan Pembatalan'),
                    'status_pembatalan' => $this->getVal($firstRow, $colIndex, 'Status Pembatalan/ Pengembalian'),
                    'no_resi' => $this->getVal($firstRow, $colIndex, 'No. Resi'),
                    'opsi_pengiriman' => $this->getVal($firstRow, $colIndex, 'Opsi Pengiriman'),
                    'metode_pengiriman' => $this->getVal($firstRow, $colIndex, 'Antar ke counter/ pick-up'),
                    'deadline_pengiriman' => $this->parseDate($this->getVal($firstRow, $colIndex, 'Pesanan Harus Dikirimkan Sebelum (Menghindari keterlambatan)')),
                    'waktu_pengiriman_diatur' => $this->parseDate($this->getVal($firstRow, $colIndex, 'Waktu Pengiriman Diatur')),
                    'waktu_pesanan_dibuat' => $waktuPesananDibuat,
                    'waktu_pembayaran' => $this->parseDate($this->getVal($firstRow, $colIndex, 'Waktu Pembayaran Dilakukan')),
                    'metode_pembayaran' => $this->getVal($firstRow, $colIndex, 'Metode Pembayaran'),
                    'voucher_penjual' => $this->parseAmount($this->getVal($firstRow, $colIndex, 'Voucher Ditanggung Penjual')),
                    'cashback_koin' => $this->parseAmount($this->getVal($firstRow, $colIndex, 'Cashback Koin')),
                    'voucher_shopee' => $this->parseAmount($this->getVal($firstRow, $colIndex, 'Voucher Ditanggung Shopee')),
                    'potongan_koin' => $this->parseAmount($this->getVal($firstRow, $colIndex, 'Potongan Koin Shopee')),
                    'diskon_kartu_kredit' => $this->parseAmount($this->getVal($firstRow, $colIndex, 'Diskon Kartu Kredit')),
                    'ongkir_pembeli' => $this->parseAmount($this->getVal($firstRow, $colIndex, 'Ongkos Kirim Dibayar oleh Pembeli')),
                    'estimasi_potongan_ongkir' => $this->parseAmount($this->getVal($firstRow, $colIndex, 'Estimasi Potongan Biaya Pengiriman')),
                    'ongkir_pengembalian' => $this->parseAmount($this->getVal($firstRow, $colIndex, 'Ongkos Kirim Pengembalian Barang')),
                    'total_pembayaran' => $this->parseAmount($this->getVal($firstRow, $colIndex, 'Total Pembayaran')),
                    'perkiraan_ongkir' => $this->parseAmount($this->getVal($firstRow, $colIndex, 'Perkiraan Ongkos Kirim')),
                    'catatan_pembeli' => $this->getVal($firstRow, $colIndex, 'Catatan dari Pembeli'),
                    'catatan' => $this->getVal($firstRow, $colIndex, 'Catatan'),
                    'username_pembeli' => $this->getVal($firstRow, $colIndex, 'Username (Pembeli)'),
                    'nama_penerima' => $this->getVal($firstRow, $colIndex, 'Nama Penerima'),
                    'no_telepon' => $this->getVal($firstRow, $colIndex, 'No. Telepon'),
                    'alamat_pengiriman' => $this->getVal($firstRow, $colIndex, 'Alamat Pengiriman'),
                    'kota' => $this->getVal($firstRow, $colIndex, 'Kota/Kabupaten'),
                    'provinsi' => $this->getVal($firstRow, $colIndex, 'Provinsi'),
                    'waktu_pesanan_selesai' => $this->parseDate($this->getVal($firstRow, $colIndex, 'Waktu Pesanan Selesai')),
                ];

                $penjualanShopee = PenjualanShopee::updateOrCreate(
                    ['no_pesanan' => $noPesanan],
                    $headerData
                );

                // Hapus detail lama jika update
                $penjualanShopee->detail()->delete();

                foreach ($orderRows as $row) {
                    $namaProduk = trim((string)$this->getVal($row, $colIndex, 'Nama Produk'));
                    if (empty($namaProduk)) {
                        continue;
                    }

                    $detailData = [
                        'penjualan_shopee_id' => $penjualanShopee->id,
                        'sku_induk' => $this->getVal($row, $colIndex, 'SKU Induk'),
                        'nama_produk' => $namaProduk,
                        'nomor_referensi_sku' => $this->getVal($row, $colIndex, 'Nomor Referensi SKU'),
                        'nama_variasi' => $this->getVal($row, $colIndex, 'Nama Variasi'),
                        'harga_awal' => $this->parseAmount($this->getVal($row, $colIndex, 'Harga Awal')),
                        'harga_setelah_diskon' => $this->parseAmount($this->getVal($row, $colIndex, 'Harga Setelah Diskon')),
                        'jumlah' => (int)$this->parseAmount($this->getVal($row, $colIndex, 'Jumlah')),
                        'returned_quantity' => (int)$this->parseAmount($this->getVal($row, $colIndex, 'Returned quantity')),
                        'subtotal_pesanan' => $this->parseAmount($this->getVal($row, $colIndex, 'Subtotal Pesanan')),
                        'total_diskon' => $this->parseAmount($this->getVal($row, $colIndex, 'Total Diskon')),
                        'diskon_penjual' => $this->parseAmount($this->getVal($row, $colIndex, 'Diskon Dari Penjual')),
                        'diskon_shopee' => $this->parseAmount($this->getVal($row, $colIndex, 'Diskon Dari Shopee')),
                        'berat_produk' => $this->getVal($row, $colIndex, 'Berat Produk'),
                        'jumlah_produk_dipesan' => (int)$this->parseAmount($this->getVal($row, $colIndex, 'Jumlah Produk di Pesan')),
                        'total_berat' => $this->getVal($row, $colIndex, 'Total Berat'),
                        'paket_diskon' => $this->getVal($row, $colIndex, 'Paket Diskon'),
                        'paket_diskon_shopee' => $this->parseAmount($this->getVal($row, $colIndex, 'Paket Diskon (Diskon dari Shopee)')),
                        'paket_diskon_penjual' => $this->parseAmount($this->getVal($row, $colIndex, 'Paket Diskon (Diskon dari Penjual)')),
                    ];

                    PenjualanShopeeDetail::create($detailData);
                    $totalDetailsImported++;
                }

                $totalOrdersImported++;
            }
        });

        return [
            'total_orders' => $totalOrdersImported,
            'total_details' => $totalDetailsImported,
        ];
    }

    private function getVal(array $row, array $colIndex, string $key): ?string
    {
        if (!isset($colIndex[$key])) {
            return null;
        }
        $val = $row[$colIndex[$key]] ?? null;
        return $val !== null ? trim((string)$val) : null;
    }

    private function parseAmount(mixed $val): float
    {
        if ($val === null || $val === '') {
            return 0.0;
        }
        // Jika format e.g. "87.500" atau "87.500,00"
        $val = str_replace('.', '', (string)$val);
        $val = str_replace(',', '.', $val);
        $val = preg_replace('/[^0-9.-]/', '', $val);
        return is_numeric($val) ? (float)$val : 0.0;
    }

    private function parseDate(mixed $val): ?string
    {
        if (empty($val)) {
            return null;
        }
        try {
            return Carbon::parse((string)$val)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
}
