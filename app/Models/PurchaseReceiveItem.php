<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseReceiveItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'purchase_receive_items';

    protected $fillable = [
        'pr_id',
        'purchase_id',
        'product_id',
        'product_variant_id',
        'brand_id',
        'cost_amount',
        'total_qty',
        'total_cost_amount',
    ];

    public function purchase()
    {
        return $this->belongsTo(PurchaseProductList::class, 'purchase_id');
    }

    public function purchaseReceive()
    {
        return $this->belongsTo(PurchaseReceive::class, 'pr_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
