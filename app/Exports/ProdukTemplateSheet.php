<?php

namespace App\Exports;

use App\Models\Kategori;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class ProdukTemplateSheet implements FromArray, WithTitle, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    public function title(): string
    {
        return 'Template';
    }

    public function headings(): array
    {
        return [
            'Kategori *',
            'Nama Barang *',
            'Variasi Barang',
            'Satuan *',
            'HPP (Rp) *',
        ];
    }

    public function array(): array
    {
        // Contoh data agar user paham formatnya
        return [
            ['Sandal Gunung', 'LAF Summit X3', 'Black/Gold - 42', 'PASANG', 120000],
            ['Sandal Santai', 'LAF Beach Breeze', 'Navy - 41', 'PASANG', 50000],
            ['Sepatu', 'LAF Urban Walker', 'Full Black - 43', 'PASANG', 185000],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22,
            'B' => 28,
            'C' => 25,
            'D' => 12,
            'E' => 16,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2F3E2F']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Ambil daftar kategori dari database
                $kategoriList = Kategori::pluck('nama_kategori')->toArray();
                $kategoriString = '"' . implode(',', $kategoriList) . '"';

                // Ambil daftar satuan umum
                $satuanString = '"PASANG,PCS,BOX,LUSIN,SET,ROLL"';

                // Terapkan dropdown validation untuk 100 baris
                for ($row = 2; $row <= 101; $row++) {
                    // Kolom A: Kategori dropdown
                    $validationA = $sheet->getCell("A{$row}")->getDataValidation();
                    $validationA->setType(DataValidation::TYPE_LIST);
                    $validationA->setErrorStyle(DataValidation::STYLE_STOP);
                    $validationA->setAllowBlank(false);
                    $validationA->setShowDropDown(true);
                    $validationA->setFormula1($kategoriString);
                    $validationA->setErrorTitle('Kategori Invalid');
                    $validationA->setError('Pilih dari daftar kategori yang tersedia.');
                    $validationA->setPromptTitle('Pilih Kategori');
                    $validationA->setPrompt('Pilih kategori dari dropdown.');
                    $validationA->setShowInputMessage(true);
                    $validationA->setShowErrorMessage(true);

                    // Kolom D: Satuan dropdown
                    $validationD = $sheet->getCell("D{$row}")->getDataValidation();
                    $validationD->setType(DataValidation::TYPE_LIST);
                    $validationD->setErrorStyle(DataValidation::STYLE_STOP);
                    $validationD->setAllowBlank(false);
                    $validationD->setShowDropDown(true);
                    $validationD->setFormula1($satuanString);
                    $validationD->setErrorTitle('Satuan Invalid');
                    $validationD->setError('Pilih salah satu: PASANG, PCS, BOX, LUSIN, SET, ROLL');
                    $validationD->setPromptTitle('Pilih Satuan');
                    $validationD->setPrompt('Pilih satuan dari dropdown.');
                    $validationD->setShowInputMessage(true);
                    $validationD->setShowErrorMessage(true);
                }

                // Style data contoh (baris 2-4) dengan warna berbeda
                $sheet->getStyle('A2:E4')->applyFromArray([
                    'font' => ['italic' => true, 'color' => ['rgb' => '888888']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF9E6']],
                ]);

                // Tambah comment/note di baris pertama data
                $sheet->getComment('A2')->getText()->createTextRun('← Hapus baris contoh ini sebelum import!')->getFont()->setBold(true);

                // Border untuk header
                $sheet->getStyle('A1:E1')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                    ],
                ]);

                // Freeze top row
                $sheet->freezePane('A2');

                // Instruction row di atas
                $sheet->insertNewRowBefore(1, 2);
                $sheet->setCellValue('A1', '📋 TEMPLATE IMPORT PRODUK — LAF PROJECT');
                $sheet->setCellValue('A2', '⚠️ Hapus baris contoh (kuning) sebelum import. Kolom bertanda * wajib diisi. Kode Barang otomatis digenerate sistem.');
                $sheet->mergeCells('A1:E1');
                $sheet->mergeCells('A2:E2');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '2F3E2F']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => 'B8860B']],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);
            },
        ];
    }
}
