<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';

    protected $fillable = [
        'customer_id',
        'order_date',
        'order_no',
        'bundle_ids',
        'name',
        'phone',
        'email',
        'user_type',
        'total_amount',
        'shipping_charges',
        'discount_amount',
        'grand_total',
        'dispatch_date',
        'order_status',
        'status',
        'rider_id',
        'pay_method',
        'rider_pay_status',
        'pay_to_company_date',
    ];

    protected $casts = [
        'bundle_ids' => 'array',
    ];

    // An order belongs to a user (customer)
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // An order has many order details
    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class);
    }

    // An order has one billing record
    public function orderBilling()
    {
        return $this->hasOne(OrderBilling::class);
    }

    public function rider()
    {
        return $this->belongsTo(Rider::class);
    }
}
