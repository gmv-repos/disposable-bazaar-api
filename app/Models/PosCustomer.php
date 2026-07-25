<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosCustomer extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'email',
        'delivery_charges',
        'image',
        'phone',
        'address',
        'area_id',
        'available_balance',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted',
        'deleted_at',
        'deleted_by',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'id');
    }

    public function stockLogs()
    {
        return $this->morphMany(StockLog::class, 'party');
    }
}
