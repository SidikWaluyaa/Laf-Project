<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kategori extends Model
{
    use SoftDeletes;
    protected $table = 'kategori';

    protected $fillable = ['nama_kategori', 'kode_prefix'];

    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class);
    }
}
