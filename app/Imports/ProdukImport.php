<?php

namespace App\Imports;

use App\Models\Kategori;
use App\Services\ProductService;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Validator;

class ProdukImport implements ToArray, WithHeadingRow, SkipsEmptyRows
{
    private ProductService $productService;
    private array $errors = [];
    private int $imported = 0;
    private int $skipped = 0;
    private array $validData = [];
    private bool $previewMode;
    private array $kategoriMap = [];

    public function __construct(bool $previewMode = false)
    {
        $this->productService = app(ProductService::class);
        $this->previewMode = $previewMode;

        // Build kategori map: nama_kategori => id
        $this->kategoriMap = Kategori::pluck('id', 'nama_kategori')->toArray();
    }

    /**
     * Custom heading row to skip instruction rows.
     */
    public function headingRow(): int
    {
        return 3; // Baris ke-3 adalah header (setelah 2 baris instruksi)
    }

    public function array(array $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 4; // +4 karena: 2 instruksi + 1 header + index starts at 0

            // Normalisasi heading — maatwebsite converts ke snake_case
            $kategoriNama = $row['kategori'] ?? $row['kategori_'] ?? null;
            $namaBarang = $row['nama_barang'] ?? $row['nama_barang_'] ?? null;
            $variasi = $row['variasi_barang'] ?? null;
            $satuan = $row['satuan'] ?? $row['satuan_'] ?? null;
            $hpp = $row['hpp_rp'] ?? $row['hpp_rp_'] ?? $row['hpp'] ?? 0;

            // Skip baris kosong
            if (empty($kategoriNama) && empty($namaBarang)) {
                continue;
            }

            // Validasi
            $errors = [];

            if (empty($kategoriNama)) {
                $errors[] = 'Kategori wajib diisi';
            } elseif (!isset($this->kategoriMap[$kategoriNama])) {
                $errors[] = "Kategori \"{$kategoriNama}\" tidak ditemukan di sistem";
            }

            if (empty($namaBarang)) {
                $errors[] = 'Nama Barang wajib diisi';
            }

            if (empty($satuan)) {
                $errors[] = 'Satuan wajib diisi';
            }

            if (!is_numeric($hpp) || $hpp < 0) {
                $errors[] = 'HPP harus angka positif';
            }

            if (!empty($errors)) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'messages' => $errors,
                    'data' => $namaBarang ?? '[kosong]',
                ];
                $this->skipped++;
                continue;
            }

            $dataToSave = [
                'kategori_id' => $this->kategoriMap[$kategoriNama],
                'kategori_nama' => $kategoriNama, // for preview display
                'nama_barang' => $namaBarang,
                'variasi_barang' => $variasi,
                'satuan' => strtoupper($satuan),
                'hpp' => (float) $hpp,
            ];

            if ($this->previewMode) {
                $this->validData[] = $dataToSave;
                $this->imported++;
                continue;
            }

            try {
                $this->productService->create([
                    'kategori_id' => $dataToSave['kategori_id'],
                    'nama_barang' => $dataToSave['nama_barang'],
                    'variasi_barang' => $dataToSave['variasi_barang'],
                    'satuan' => $dataToSave['satuan'],
                    'hpp' => $dataToSave['hpp'],
                ]);

                $this->imported++;
            } catch (\Exception $e) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'messages' => [$e->getMessage()],
                    'data' => $namaBarang,
                ];
                $this->skipped++;
            }
        }
    }

    public function getValidData(): array
    {
        return $this->validData;
    }

    public function getImported(): int
    {
        return $this->imported;
    }

    public function getSkipped(): int
    {
        return $this->skipped;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
