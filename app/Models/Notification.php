<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = ['type', 'product_id', 'message', 'data', 'url', 'is_read'];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sell()
    {
        return $this->belongsTo(Sell::class)->when($this->type === 'sell_payment_date', function ($query) {
            $query->where('id', $this->record_id);
        });
    }
}
