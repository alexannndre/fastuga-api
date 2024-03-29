<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $dates = ['updated_at', 'created_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ticket_number',
        'status',
        'customer_id',
        'total_price',
        'total_paid',
        'total_paid_with_points',
        'points_gained',
        'points_used_to_pay',
        'payment_type',
        'payment_reference',
        'date'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'datetime',
        'custom' => 'json'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function delivered()
    {
        return $this->belongsTo(User::class, 'delivered_by');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items', 'order_id', 'product_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cancelReason()
    {
        if (isset($this->custom)) {
            if (array_key_exists('cancel_reason', $this->custom))
                return $this->custom['cancel_reason'];
        }
        return null;
    }

    public function allDishesReady()
    {
        return !($this->items->where('status', '!=', 'R')->count() > 0);
    }
}
