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
        'publication_year',
        'isbn',
        'price',
        'stock_quantity',
        'description',
        'cover_image',
        'is_featured',
    ];

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
}