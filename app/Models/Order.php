<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'table_id', 'total_amount', 'status', 
        'notes', 'order_date', 'estimated_completion_time', 'user_id'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'order_date' => 'datetime',
        'estimated_completion_time' => 'datetime',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function dishes(): BelongsToMany
    {
        return $this->belongsToMany(Dish::class, 'order_items')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function calculateTotal()
    {
        return $this->orderItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }

    public function calculateCookingTime()
    {
        return $this->dishes->max('cooking_time');
    }

    public function getStatusColor()
    {
        return match($this->status) {
            'pending' => 'warning',
            'preparing' => 'info',
            'ready' => 'success',
            'served' => 'primary',
            'paid' => 'secondary',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusText()
    {
        return match($this->status) {
            'pending' => 'Ожидает',
            'preparing' => 'Готовится',
            'ready' => 'Готов',
            'served' => 'Подано',
            'paid' => 'Оплачено',
            'cancelled' => 'Отменено',
            default => 'Неизвестно',
        };
    }
}
