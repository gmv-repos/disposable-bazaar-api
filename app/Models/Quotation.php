<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $table = 'quotations';
    protected $fillable = [
        'quotation_date',
        'reference_code',
        // 'customer_id',
        'customer_name',
        'company_name',
        'status',
        'total',
        'discount',
        'delivery_charges',
        'tax',
        'payable_amount',
        'notes',
        'valid_until',
    ];

    public function customer()
    {
        return $this->belongsTo(PosCustomer::class, 'customer_id');
    }

    public function quotationItems()
    {
        return $this->hasMany(QuotationItem::class, 'quotation_id');
    }
}
