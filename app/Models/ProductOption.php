<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    use HasFactory;
    protected $table = 'product_options';

    protected $fillable = ['product_id', 'size_id', 'option_id', 'options_price', 'status'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function size()
    {
        return $this->belongsTo(ProductSize::class, 'size_id');
    }

    public function option()
    {
        return $this->belongsTo(Option::class, 'option_id');
    }
}
