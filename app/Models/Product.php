<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'category',
        'brand',
        'size',
        'condition',
        'price',
        'stock',
        'description',
        'tags',
        'status',
        'image_path',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    // ── Scopes ──────────────────────────────────────────
    public function scopeAvailable($query)
    {
        return $query->where('status', 'Tersedia');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('name', 'ilike', "%{$keyword}%")
              ->orWhere('sku', 'ilike', "%{$keyword}%")
              ->orWhere('brand', 'ilike', "%{$keyword}%")
              ->orWhere('tags', 'ilike', "%{$keyword}%");
        });
    }

    public function scopeLowStock($query, $threshold = 2)
    {
        return $query->where('stock', '<=', $threshold)
                     ->where('stock', '>', 0);
    }

    // ── Accessors ────────────────────────────────────────
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock <= 2 && $this->stock > 0;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->stock === 0;
    }

    // ── SKU Generator ────────────────────────────────────
    public static function generateSku(): string
    {
        $last = static::withTrashed()->latest('id')->value('id') ?? 0;
        return 'TS-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }
}
