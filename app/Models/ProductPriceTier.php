<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPriceTier extends Model
{
    protected $fillable = ['product_id', 'min_qty', 'max_qty', 'price'];

    protected $casts = ['price' => 'decimal:0'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getLabelAttribute(): string
    {
        $min = number_format($this->min_qty, 0, ',', '.');
        $max = $this->max_qty ? number_format($this->max_qty, 0, ',', '.') : '+';
        return "{$min}–{$max} pcs";
    }

    public function getPriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
