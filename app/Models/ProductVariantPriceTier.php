<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ProductVariantPriceTier extends Model
{
    protected $fillable = ['product_variant_id', 'min_qty', 'max_qty', 'price'];
    protected $casts = ['price' => 'decimal:0'];
    public function variant(): BelongsTo {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
    public function getPriceFormattedAttribute(): string {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
