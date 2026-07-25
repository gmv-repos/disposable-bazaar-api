<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rider extends Model
{
    use HasFactory;

    protected $table = 'riders';

    protected $fillable = ['name', 'email', 'phone', 'address', 'status', 'earning', 'paid'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function sells()
    {
        return $this->hasMany(Sell::class);
    }

    public function riderPayments()
    {
        return $this->hasMany(RiderPayment::class);
    }
}
