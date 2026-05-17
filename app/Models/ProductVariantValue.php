<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ProductVariantValue extends Model
{
    protected $fillable = ['product_variant_option_id', 'value', 'sort_order'];
    public function option(): BelongsTo {
        return $this->belongsTo(ProductVariantOption::class, 'product_variant_option_id');
    }
}
