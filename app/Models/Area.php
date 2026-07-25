<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
    protected $table = 'areas';

    protected $fillable = ['area_name', 'city_name', 'shipping_rate', 'status'];

    // One Area can have many Order Billings
    public function orderBillings()
    {
        return $this->hasMany(OrderBilling::class);
    }
}
