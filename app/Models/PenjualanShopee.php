<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanShopee extends Model
{
    use HasFactory;

    protected $table = 'penjualan_shopee';

    protected $guarded = ['id'];

    public function detail()
    {
        return $this->hasMany(PenjualanShopeeDetail::class, 'penjualan_shopee_id');
    }
}
