<?php

namespace Src\Products\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PriceHistory extends Model
{
    use HasUuids;

    protected $table = 'price_histories';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'price',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
