<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokMinimum extends Model
{
    protected $table = 'stok_minimum';

    protected $fillable = [
        'produk_id',
        'stok_minimum',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }
}
