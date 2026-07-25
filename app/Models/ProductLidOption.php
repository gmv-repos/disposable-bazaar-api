<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductLidOption extends Model
{
    use HasFactory;

    protected $table = 'product_lid_options';

    protected $fillable = ['product_id', 'lid_option_id', 'price'];

    public function lidOption()
    {
        return $this->belongsTo(LidOption::class, 'lid_option_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
