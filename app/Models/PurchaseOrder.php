<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $table = 'purchase_order';

    protected $fillable = [
        'tanggal',
        'supplier_id',
        'nomor_po',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function detail(): HasMany
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }
}
