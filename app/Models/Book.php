<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Contracts\Auditable;

class Book extends Model implements Auditable
{
    use HasFactory, Searchable, \OwenIt\Auditing\Auditable;

    protected $auditExclude = ['cover_image', 'cover_image_url', 'updated_at'];

    protected $fillable = [
        'category_id',
        'title',
        'author',
        'publisher',
        'publication_year',
        'isbn',
        'price',
        'stock_quantity',
        'description',
        'cover_image',
        'cover_image_url',
        'is_featured',
        'format',
        'published_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'date',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'price' => 'decimal:2',
        ];
    }

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

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'publisher' => $this->publisher,
            'description' => $this->description,
            'category' => $this->category?->name,
            'format' => $this->format,
            'isbn' => $this->isbn,
        ];
    }

    public function shouldBeSearchable(): bool
    {
        return $this->is_active;
    }
}
