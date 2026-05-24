<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductVariantValue extends Model
{
    protected $fillable = ['product_variant_option_id', 'value', 'image', 'sort_order'];

    public function option(): BelongsTo {
        return $this->belongsTo(ProductVariantOption::class, 'product_variant_option_id');
    }

    public function getImageUrlAttribute(): ?string {
        return $this->image ? Storage::url($this->image) : null;
    }
}
