<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangMasukDetail extends Model
{
    protected $table = 'barang_masuk_detail';

    protected $fillable = [
        'barang_masuk_id',
        'produk_id',
        'qty_masuk',
        'harga_beli',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
    ];

    public function barangMasuk(): BelongsTo
    {
        return $this->belongsTo(BarangMasuk::class);
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }
}
