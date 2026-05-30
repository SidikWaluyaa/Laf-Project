<?php

namespace App\Exports;

use App\Models\Kategori;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProdukReferensiSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    public function title(): string
    {
        return 'Referensi';
    }

    public function headings(): array
    {
        return [
            'Nama Kategori',
            'Kode Prefix',
            'Format Kode',
        ];
    }

    public function collection()
    {
        return Kategori::all()->map(function ($k) {
            return [
                'nama_kategori' => $k->nama_kategori,
                'kode_prefix' => $k->kode_prefix,
                'format_kode' => 'LAF-' . $k->kode_prefix . '-XXX',
            ];
        });
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 15,
            'C' => 20,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->setCellValue('A' . ($sheet->getHighestRow() + 2), '📌 Sheet ini hanya referensi. Isi data di sheet "Template".');
        $sheet->getStyle('A' . ($sheet->getHighestRow()))->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF888888'));

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '333333']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
