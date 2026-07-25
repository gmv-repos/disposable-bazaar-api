<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'serial_no',
        'parent_id',
        'name',
        'slug',
        'image',
        'hero_banner_image',
        'note',
        'is_popular',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted',
        'deleted_at',
        'deleted_by',
    ];
    protected $appends = ['category_icon'];

    public function getCategoryIconAttribute()
    {
        if ($this->image) {
            return $this->image;
        } else {
            return 'storage/category_icons/empty2.png';
        }
    }

    public function parentCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    // Self-relation for child categories
    public function childCategories()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id')
            ->orderByRaw('ISNULL(serial_no), serial_no ASC');
    }

    public function subcategory()
    {
        return $this->hasMany(ProductSubCategory::class, 'category_id', 'id');
    }

    public function categorySeoMetadata()
    {
        return $this->hasOne(CategorySeoMetadata::class, 'category_id');
    }


    public function sortOrders()
    {
        return $this->hasMany(CategoriesSortOrder::class, 'category_id');
    }


    // public function scopeWithSortOrderForSection($query, $sectionName)
    // {
    //     return $query->leftJoin('categories_sort_orders as cso', function ($join) use ($sectionName) {
    //         $join->on('cso.category_id', '=', 'product_categories.id')
    //             ->where('cso.section_name', '=', $sectionName);
    //     })
    //         ->select('product_categories.*', 'cso.sort_order')
    //         ->orderByRaw('CASE WHEN cso.sort_order IS NULL THEN 1 ELSE 0 END, cso.sort_order ASC')
    //         ->orderBy('product_categories.name', 'ASC');
    // }

    public function scopeWithSortOrderForSection($query, $sectionName, $includeNulls = false)
    {
        $query->leftJoin('categories_sort_orders as cso', function ($join) use ($sectionName) {
            $join->on('cso.category_id', '=', 'product_categories.id')
                ->where('cso.section_name', '=', $sectionName);
        })
            ->select('product_categories.*', 'cso.sort_order', 'cso.is_visible');

        if ($includeNulls) {
            $query->whereNull('cso.sort_order');
        }

        $query->where(function ($q) {
            $q->whereNull('cso.category_id')
                ->orWhere('cso.is_visible', 1);
        });

        $query->orderByRaw('CASE WHEN cso.sort_order IS NULL THEN 1 ELSE 0 END')
            ->orderBy('cso.sort_order', 'ASC')
            ->orderBy('product_categories.name', 'ASC');

        return $query;
    }
}
