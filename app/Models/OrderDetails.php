<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;

    protected $table = 'order_details';

    protected $fillable = [
        'order_id',
        'product_id',
        'product_lid_option_id',
        'pack_size',
        'qty',
        'total_pieces',
        'product_sub_total',
        'is_customize',
        'product_option_id',
        'customize_logo_image',
        'packaging_options',
        'additional_customization',
    ];

    protected $casts = [
        'packaging_options' => 'array'
    ];

    // Order Detail belongs to an order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Order Detail belongs to a product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}