<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bundle extends Model
{
    use HasFactory;

    protected $table = 'bundles';

    protected $fillable = [
        'reference_code',
        'name',
        'slug',
        'total_amount',
        'discount_amount',
        'delivery_charges',
        'payable_amount',
        'description',
        'main_image',
        'meta_title',
        'canonical_url',
        'focus_keyword',
        'redirect_301',
        'redirect_302',
        'schema',
        'status',
    ];

    public function bundleItems()
    {
        return $this->hasMany(BundleItem::class, 'bundle_id');
    }
    public function bundleImages()
    {
        return $this->hasMany(BundleImage::class, 'bundle_id');
    }
}
