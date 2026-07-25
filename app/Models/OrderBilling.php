<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderBilling extends Model
{
    use HasFactory;

    protected $table = 'order_billings';

    protected $fillable = ['order_id', 'area_id', 'address', 'special_instructions'];

    // Order Billing belongs to an order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Order Billing belongs to an area
    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
