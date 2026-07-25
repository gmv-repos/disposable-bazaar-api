<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $table = 'discounts';
    protected $fillable = ['name', 'discount_percentage', 'start_time', 'end_time', 'is_active'];

    public function item()
    {
        return $this->hasMany(DiscountItem::class);
    }
}
