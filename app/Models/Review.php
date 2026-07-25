<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'rating',
        'title_of_review',
        'description',
        'user_id',
        'do_your_recomended_this_product',
        'image',
        'product_id',
        'created_at',
        'updated_at',
    ];

    public function likes()
    {
        return $this->hasMany(ReviewLike::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
