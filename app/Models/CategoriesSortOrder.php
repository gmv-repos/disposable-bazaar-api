<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriesSortOrder extends Model
{

    use HasFactory;
    protected $table = 'categories_sort_orders';
    protected $fillable = ['section_name', 'category_id', 'sort_order', 'is_visible'];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
