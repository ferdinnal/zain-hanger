<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class ProductVariantOption extends Model
{
    protected $fillable = ['product_id', 'name', 'sort_order'];
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function values(): HasMany {
        return $this->hasMany(ProductVariantValue::class, 'product_variant_option_id')->orderBy('sort_order');
    }
}
