<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'detail',
        'sizes',
        'color',
        'discount',
        'price',
        'image',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount' => 'integer',
            'sizes' => 'array',
        ];
    }

    /**
     * Calculate price after discount if discount is applied.
     */
    public function getDiscountedPriceAttribute(): float
    {
        $basePrice = (float) $this->price;
        if ($this->discount && $this->discount > 0) {
            $discountAmount = ($basePrice * $this->discount) / 100;
            return round($basePrice - $discountAmount, 2);
        }
        return $basePrice;
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
