<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get the menu items under this category.
     */
    public function menuItems()
    {
        return $this->hasMany(MenuItem::class, 'category', 'name');
    }
}
