<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'company_name',
        'contact_no',
        'location',
        'email',
        'product_id',
        'logo_design',
        'created_at',
        'updated_at',
    ];
}
