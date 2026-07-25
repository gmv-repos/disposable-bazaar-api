<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getPendingQuantityAttribute()
    {
        return $this->quantity - $this->delivered_quantity;
    }

    public function itemVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
