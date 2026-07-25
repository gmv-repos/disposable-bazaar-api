<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPackagingOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'print_location',
        'side_option',
        'price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}