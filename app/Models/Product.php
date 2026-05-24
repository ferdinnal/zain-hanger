<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description',
        'image', 'image_url', 'jenis', 'kepala',
        'is_anti_theft', 'is_featured', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_anti_theft' => 'boolean',
        'is_featured'   => 'boolean',
        'is_active'     => 'boolean',
    ];

    const JENIS_LABELS = [
        'polos'               => 'Polos',
        'palang_kayu'         => 'Palang Kayu',
        'celana'              => 'Celana',
        'palang_jepit'        => 'Palang Jepit',
        'celana_palang_jepit' => 'Celana Palang Jepit',
    ];

    const KEPALA_LABELS = [
        'silver'         => 'Hook Silver Biasa',
        'gold_10'        => 'Hook Gold 10cm',
        'gold_15'        => 'Hook Gold 15cm',
        'gold_20'        => 'Hook Gold 20cm',
        'plat_gold_10'   => 'Plat Gold 10cm',
        'plat_gold_15'   => 'Plat Gold 15cm',
        'plat_silver_10' => 'Plat Silver 10cm',
    ];

    // ── Relations ──────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function priceTiers(): HasMany
    {
        return $this->hasMany(ProductPriceTier::class)->orderBy('min_qty');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Relasi Gambar & Variasi ─────────────────────────────────

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)
                    ->where('is_primary', true)
                    ->orderBy('sort_order');
    }

    public function variantOptions(): HasMany
    {
        return $this->hasMany(ProductVariantOption::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)
                    ->where('is_active', true)
                    ->orderBy('sort_order');
    }

    public function hasVariants(): bool
    {
        return $this->variants()->exists();
    }

    // ── Accessors ───────────────────────────────────────────────

    public function getJenisLabelAttribute(): ?string
    {
        return self::JENIS_LABELS[$this->jenis] ?? null;
    }

    public function getKepalaLabelAttribute(): ?string
    {
        return self::KEPALA_LABELS[$this->kepala] ?? null;
    }

    public function getImageUrlAttribute($value): string
    {
        if ($this->image) {
            return Storage::url($this->image);
        }
        return $value ?? 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?q=80&w=400';
    }

    public function getDisplayImageAttribute(): string
    {
        if ($this->relationLoaded('primaryImage') && $this->primaryImage) {
            return $this->primaryImage->url;
        }
        if ($this->relationLoaded('images') && $this->images->first()) {
            return $this->images->first()->url;
        }
        if ($this->image) return Storage::url($this->image);
        return $this->attributes['image_url']
            ?? 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?q=80&w=400';
    }

    // ── Scopes ──────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['q'] ?? null, fn ($q, $search) =>
            $q->where('name', 'like', "%{$search}%")
        );

        $query->when($filters['category'] ?? null, fn ($q, $slug) =>
            $q->whereHas('category', fn ($c) => $c->where('slug', $slug))
        );

        $query->when($filters['jenis'] ?? null, fn ($q, $val) =>
            $q->where('jenis', $val)
        );

        $query->when($filters['kepala'] ?? null, fn ($q, $val) =>
            $q->where('kepala', $val)
        );

        $query->when($filters['type'] ?? null, function ($q, $val) {
            if ($val === 'anti_theft') $q->where('is_anti_theft', true);
            if ($val === 'standard')   $q->where('is_anti_theft', false);
        });

        return $query;
    }

    // ── Helpers ─────────────────────────────────────────────────

    public function getPriceForQty(int $qty): ?ProductPriceTier
    {
        return $this->priceTiers
            ->filter(fn ($t) =>
                $qty >= $t->min_qty &&
                ($t->max_qty === null || $qty <= $t->max_qty)
            )
            ->first();
    }

    public function toSnapshot(): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'jenis'         => $this->jenis,
            'jenis_label'   => $this->jenis_label,
            'kepala'        => $this->kepala,
            'kepala_label'  => $this->kepala_label,
            'is_anti_theft' => $this->is_anti_theft,
            'image_url'     => $this->display_image,
            'category'      => $this->category?->name,
        ];
    }

    // Tambah di Product.php
public function allVariants(): HasMany
{
    return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
}

}
