<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogsSeoMetadata extends Model
{
    use HasFactory;

    protected $table = 'blogs_seo_metadata';

    protected $fillable = [
        'blog_id',
        'meta_title',
        'canonical_url',
        'focus_keyword',
        'redirect_301',
        'redirect_302',
        'schema',
        'meta_description',
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id');
    }
}
