<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantSize extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'size_id',
        'description',
    ];

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id', 'id');
    }

    public function size()
    {
        return $this->belongsTo(ProductSize::class, 'size_id', 'id');
    }
}
