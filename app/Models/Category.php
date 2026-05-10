<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'image',
        'image_url', 'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function getImageUrlAttribute($value): string
    {
        if ($this->image) {
            return Storage::url($this->image);
        }
        return $value ?? 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?q=80&w=800';
    }
}
