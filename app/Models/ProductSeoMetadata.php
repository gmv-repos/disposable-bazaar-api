<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSeoMetadata extends Model
{
    use HasFactory;

    protected $table = 'product_seo_metadata';

    protected $fillable = [
        'product_id',
        'meta_title',
        'canonical_url',
        'focus_keyword',
        'redirect_301',
        'redirect_302',
        'schema',
        'meta_description',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
