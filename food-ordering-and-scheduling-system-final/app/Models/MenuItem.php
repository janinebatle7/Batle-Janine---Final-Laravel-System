<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'price', 'category', 'image', 'availability_status'
    ];

    protected $casts = [
        'availability_status' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
