<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Book extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $auditExclude = ['cover_image', 'updated_at'];

    protected $fillable = [
        'category_id',
        'title',
        'author',
        'publisher',
        'format',
        'publication_year',
        'published_at',
        'isbn',
        'price',
        'stock_quantity',
        'description',
        'cover_image',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'published_at' => 'date',
        'is_featured'  => 'boolean',
        'is_active'    => 'boolean',
        'price'        => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Computed Attributes ───────────────────────────────────────────────────

    public function getAverageRatingAttribute(): float
    {
        return (float) ($this->reviews()->avg('rating') ?? 0);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    /**
     * Only return active books (is_active = true).
     * Used in all public-facing catalog queries.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Optimized catalog query using covering index columns only.
     * Avoids SELECT * on 1M+ row tables.
     */
    public function scopeCatalog($query)
    {
        return $query->select([
            'id', 'isbn', 'title', 'author', 'publisher',
            'price', 'stock_quantity', 'published_at',
            'category_id', 'format', 'is_active', 'is_featured',
        ]);
    }
}
