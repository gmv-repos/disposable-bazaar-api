<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewLike extends Model
{
    use HasFactory;
    protected $fillable = ['review_id', 'user_id', 'is_like'];

    // Define the relationship to the Review
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    // Define the relationship to the User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
