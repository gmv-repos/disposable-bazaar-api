<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LidOption extends Model
{
    use HasFactory;

    protected $table = 'lid_options';

    protected $fillable = ['name', 'image', 'img_alt', 'img_name'];

    public function productLidOptions()
    {
        return $this->hasMany(ProductLidOption::class, 'lid_option_id');
    }
}
