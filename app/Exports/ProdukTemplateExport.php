<?php

namespace App\Exports;

use App\Models\Kategori;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProdukTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Template' => new ProdukTemplateSheet(),
            'Referensi' => new ProdukReferensiSheet(),
        ];
    }
}
