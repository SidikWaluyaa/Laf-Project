<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Satuan extends Model
{
    use SoftDeletes;
    protected $table = 'satuan';

    protected $fillable = [
        'nama_satuan',
        'keterangan',
    ];

    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class);
    }
}
