<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produk extends Model
{
    use SoftDeletes;

    protected $table = 'produk';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'variasi_barang',
        'kategori_id',
        'satuan_id',
        'satuan',
        'hpp',
    ];

    protected $casts = [
        'hpp' => 'decimal:2',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function satuanRelasi(): BelongsTo
    {
        return $this->belongsTo(Satuan::class, 'satuan_id');
    }

    /**
     * Get display label for satuan (prefer FK, fallback to text column).
     */
    public function getSatuanLabelAttribute(): string
    {
        return $this->satuanRelasi?->nama_satuan ?? $this->satuan ?? '-';
    }

    public function stokProduk(): HasMany
    {
        return $this->hasMany(StokProduk::class);
    }

    public function stokMinimum(): HasOne
    {
        return $this->hasOne(StokMinimum::class);
    }

    public function barangMasukDetail(): HasMany
    {
        return $this->hasMany(BarangMasukDetail::class);
    }

    public function penjualanDetail(): HasMany
    {
        return $this->hasMany(PenjualanDetail::class);
    }

    public function purchaseOrderDetail(): HasMany
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    /**
     * Get total stok across all locations.
     */
    public function getTotalStokAttribute(): int
    {
        return $this->stokProduk->sum('total_stok');
    }

    /**
     * Get nilai (value) = hpp × total_stok.
     */
    public function getNilaiAttribute(): float
    {
        return $this->hpp * $this->total_stok;
    }
}
