<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'sku', 'combination', 'is_active', 'sort_order'];
    protected $casts = ['combination' => 'array', 'is_active' => 'boolean'];
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function priceTiers(): HasMany {
        return $this->hasMany(ProductVariantPriceTier::class)->orderBy('min_qty');
    }
    public function getPriceForQty(int $qty): ?ProductVariantPriceTier {
        return $this->priceTiers->filter(fn($t) =>
            $qty >= $t->min_qty && ($t->max_qty === null || $qty <= $t->max_qty)
        )->first();
    }
    public function getCombinationLabelAttribute(): string {
        return collect($this->combination)->map(fn($v, $k) => "{$k}: {$v}")->join(' | ');
    }
    public function getMinPriceAttribute(): float {
        return $this->priceTiers->min('price') ?? 0;
    }
}
