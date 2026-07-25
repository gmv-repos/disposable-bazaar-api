<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $table = 'pages';

    protected $fillable = [
        'page_id',
        'name',
        'slug',
        'meta_title',
        'canonical_url',
        'focus_keyword',
        'redirect_301',
        'redirect_302',
        'schema',
        'meta_description',
        'page_content',
    ];
}
