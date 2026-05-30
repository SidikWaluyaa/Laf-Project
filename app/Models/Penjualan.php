<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penjualan extends Model
{
    use SoftDeletes;

    protected $table = 'penjualan';

    protected $fillable = [
        'tanggal',
        'tipe_nota',
        'pelanggan_id',
        'nomor_nota',
        'keterangan',
        'admin_id',
        'lokasi_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class);
    }

    public function detail(): HasMany
    {
        return $this->hasMany(PenjualanDetail::class);
    }
}
