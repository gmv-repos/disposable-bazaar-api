<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Cviebrock\EloquentSluggable\Sluggable;
// use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Blog extends Model
{
    use HasFactory;
    protected $fillable = ['category_id', 'date', 'title', 'body', 'slug', 'image'];

    // Define the relationship to the User model (author)
    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    // public function sluggable(): array
    // {
    //     return [
    //         'slug' => [
    //             'source' => 'title',
    //             'onUpdate' => true // Optionally update the slug when the title changes
    //         ]
    //     ];
    // }

    function blogImage()
    {
        return $this->hasMany(BlogImage::class, 'blog_id', 'id');
    }

    public function blogSeoMetadata()
    {
        return $this->hasOne(BlogsSeoMetadata::class);
    }
}
