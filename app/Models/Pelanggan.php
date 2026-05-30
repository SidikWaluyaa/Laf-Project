<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pelanggan extends Model
{
    use SoftDeletes;
    protected $table = 'pelanggan';

    protected $fillable = ['nama_pelanggan'];

    public function penjualan(): HasMany
    {
        return $this->hasMany(Penjualan::class);
    }
}
