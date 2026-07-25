<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'unit_type',
        'serial_no',
        'parent_product_id',
        'name',
        'no_of_piece_qty_in_carton',
        'slug',
        'category_id',
        'subcategory_id',
        'image_path',
        'image_alt',
        'image_name',
        'supplier_id',
        'code',
        'color',
        'size',
        'brand_id',
        'current_sale_price',
        'current_purchase_cost',
        'current_wholesale_price',
        'wholesale_minimum_qty',
        'previous_wholesale_price',
        'previous_sale_price',
        'previous_purchase_cost',
        'available_quantity',
        'discount_type',
        'discount',
        'unit_type',
        'description',
        'is_popular',
        'is_trending',
        'is_customizeable',
        'product_video_url',
        'additional_information',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted',
        'deleted_at',
        'deleted_by',
        'stock_alert',
        'order_limit',
        'stock_status',
    ];

    protected $casts = [
        'available_quantity' => 'integer',
        'is_customizeable' => 'boolean',
    ];

    public function parentProduct()
    {
        return $this->belongsTo(Product::class, 'parent_product_id');
    }

    public function childProducts()
    {
        return $this->hasMany(Product::class, 'parent_product_id');
    }


    public function categories()
    {
        return $this->belongsToMany(ProductCategory::class, 'product_category', 'product_id', 'category_id');
    }

    function productCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }
    function categorySeoDetail()
    {
        return $this->belongsTo(CategorySeoMetadata::class, 'category_id', 'category_id');
    }

    function productSubcategory()
    {
        return $this->belongsTo(ProductSubCategory::class, 'subcategory_id', 'id');
    }

    function productImage()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    function productVariants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id')->orderBy('serial_no');
    }

    function productReviews()
    {
        return $this->hasMany(Review::class, 'product_id', 'id');
    }

    function productOptions()
    {
        return $this->hasMany(ProductOption::class, 'product_id', 'id');
    }

    function carts()
    {
        return $this->hasMany(Cart::class, 'product_id', 'id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class);
    }

    public function sellDetails()
    {
        return $this->hasMany(Sell_details::class);
    }

    public function seoMetadata()
    {
        return $this->hasOne(ProductSeoMetadata::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function productLidOptions()
    {
        return $this->hasMany(ProductLidOption::class);
    }

    public function discountItems()
    {
        return $this->hasMany(DiscountItem::class, 'product_id');
    }

    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }


    public function packagingOptions()
    {
        return $this->hasMany(ProductPackagingOption::class);
    }

    public function scopeCustomizeable($query)
    {
        return $query->where('is_customizeable', true);
    }

    public function scopeNormal($query)
    {
        return $query->where('is_customizeable', false);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeNotDeleted($query)
    {
        return $query->where('deleted', 0);
    }

    public function scopeActiveAndNotDeleted($query)
    {
        return $query->active()->notDeleted();
    }
}
