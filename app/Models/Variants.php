<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variants extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'name', 'pack_size'];

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class, 'variant_id');
    }
}
