<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;
    protected $fillable = [
        'serial_no',
        'product_id',
        'price',
        'variant_id',
        'price_per_peice',
        'brand_id',
        'status',
        'stock_status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Add this relationship to associate a ProductVariant with a Variant
    public function variant()
    {
        return $this->belongsTo(Variants::class, 'variant_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function variantDiscounts()
    {
        return $this->hasMany(Discount::class);
    }
    public function variantDiscountsActive()
    {
        return $this->hasMany(Discount::class)->where('is_active', true);
    }

    public function PurchaseDetails()
    {
        return $this->hasMany(PurchaseDetails::class);
    }

    public function variantSizes()
    {
        return $this->hasMany(ProductVariantSize::class);
    }
}
