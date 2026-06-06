<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model {
    protected $fillable = [
        'user_id', 'total_amount', 'order_type', 
        'scheduled_datetime', 'status', 'payment_method', 'payment_status'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany {
        return $this->hasMany(OrderDetail::class);
    }

    public function payment(): BelongsTo {
        return $this->hasOne(Payment::class);
    }

    // Scope for Kitchen: Prioritize scheduled and recently confirmed
    public function scopeActive($query) {
        return $query->whereIn('status', ['Confirmed', 'Preparing', 'Ready'])
                     ->orderBy('scheduled_datetime', 'asc')
                     ->orderBy('created_at', 'asc');
    }
}

class OrderDetail extends Model {
    protected $fillable = ['order_id', 'menu_item_id', 'quantity', 'subtotal', 'special_instructions'];

    public function menuItem(): BelongsTo {
        return $this->belongsTo(MenuItem::class);
    }
}

class MenuItem extends Model {
    protected $fillable = ['name', 'description', 'price', 'category', 'image', 'availability_status'];

    public function scopeAvailable($query) {
        return $query->where('availability_status', true);
    }
}

class Payment extends Model {
    protected $fillable = [
        'order_id', 'payment_method', 'amount', 
        'payment_status', 'reference_number', 'proof_image', 'verified_by'
    ];
}
