<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image',
        'stock',
        'is_active',
        'has_discount',
        'discount_percentage',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'is_active' => 'boolean',
        'has_discount' => 'boolean',
        'stock' => 'integer',
    ];

    protected $appends = ['final_price', 'image_url'];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the final price after discount.
     */
    public function getFinalPriceAttribute(): float
    {
        if ($this->has_discount && $this->discount_percentage > 0) {
            return round($this->price * (1 - $this->discount_percentage / 100), 2);
        }
        return $this->price;
    }

    /**
     * Get the full image URL.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        
        // If image is already a full URL, return as is
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }
        
        // Extract just the filename
        $filename = basename($this->image);
        return url("/api/products/image/{$filename}");
    }

    /**
     * Check if product has enough stock.
     */
    public function hasStock(int $quantity): bool
    {
        return $this->stock >= $quantity;
    }

    /**
     * Reduce product stock.
     */
    public function reduceStock(int $quantity): bool
    {
        if (!$this->hasStock($quantity)) {
            return false;
        }
        
        $this->stock -= $quantity;
        $this->save();
        return true;
    }
}
