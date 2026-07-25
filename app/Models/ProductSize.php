<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    use HasFactory;
    protected $fillable = ['size'];

    public function productSizes()
    {
        return $this->hasMany(ProductOption::class, 'size_id');
    }
}
