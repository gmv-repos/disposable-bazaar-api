<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'id',
        'supplier_name',
        'image',
        'supplier_phone_one',
        'supplier_phone_two',
        'company_name',
        'company_address',
        'supplier_address',
        'company_email',
        'company_phone',
        'supplier_email',
        'previous_due',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted',
        'deleted_at',
        'deleted_by',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'supplier_id', 'id');
    }

    public function stockLogs()
    {
        return $this->morphMany(StockLog::class, 'party');
    }
}
