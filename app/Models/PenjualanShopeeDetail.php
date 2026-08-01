<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanShopeeDetail extends Model
{
    use HasFactory;

    protected $table = 'penjualan_shopee_detail';

    protected $guarded = ['id'];

    public function penjualanShopee()
    {
        return $this->belongsTo(PenjualanShopee::class, 'penjualan_shopee_id');
    }
}
