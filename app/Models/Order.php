<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'delivery_address',
        'payment_method',
        'payment_status',
        'order_status',
        'subtotal',
        'delivery_fee',
        'total',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the order items for the order.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Calculate order total from items.
     */
    public function calculateTotal(): void
    {
        $this->subtotal = $this->orderItems->sum('subtotal');
        $this->total = $this->subtotal + $this->delivery_fee;
        $this->save();
    }

    /**
     * Get order with items and product details.
     */
    public static function getOrderDetails(int $orderId)
    {
        return self::with(['orderItems.product.category'])
            ->findOrFail($orderId);
    }

    /**
     * Update order status.
     */
    public function updateStatus(string $status): bool
    {
        $validStatuses = ['pendiente', 'preparando', 'enviado', 'entregado', 'cancelado'];
        
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        $this->order_status = $status;
        return $this->save();
    }

    /**
     * Update payment status.
     */
    public function updatePaymentStatus(string $status): bool
    {
        $validStatuses = ['pendiente', 'pagado', 'rechazado'];
        
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        $this->payment_status = $status;
        return $this->save();
    }
}
