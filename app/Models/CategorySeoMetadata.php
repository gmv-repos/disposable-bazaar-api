<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorySeoMetadata extends Model
{
    use HasFactory;

    protected $table = 'category_seo_metadata';

    protected $fillable = [
        'category_id',
        'meta_title',
        'canonical_url',
        'focus_keyword',
        'redirect_301',
        'redirect_302',
        'schema',
        'meta_description',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }
}
