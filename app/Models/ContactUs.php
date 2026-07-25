<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    protected $table = 'contacts';
    use HasFactory;
    protected $fillable = ['full_name', 'mobile_no', 'email', 'message', 'created_at', 'updated_at'];
}
