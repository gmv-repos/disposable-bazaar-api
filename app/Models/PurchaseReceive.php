<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PurchaseReceive extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'purchase_receive';

    protected $fillable = ['pr_code', 'purchase_id', 'supplier_id', 'payable_amount', 'paid_amount', 'due_amount'];

    public function purchase()
    {
        return $this->belongsTo(PurchaseProductList::class, 'purchase_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function prItems()
    {
        return $this->hasMany(PurchaseReceiveItem::class, 'pr_id');
    }
}
