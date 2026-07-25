<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLog extends Model
{
    use HasFactory;

    const TYPE_IN = 'in';
    const TYPE_OUT = 'out';

    protected $fillable = ['type', 'stock_id', 'party_type', 'party_id', 'product_id', 'brand_id', 'price', 'qty'];

    protected $casts = [
        'price' => 'decimal:2',
        'qty' => 'integer',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function party()
    {
        return $this->morphTo();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
