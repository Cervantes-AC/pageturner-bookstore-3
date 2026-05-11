<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'isbn' => $this->isbn,
            'title' => $this->title,
            'author' => $this->author,
            'publisher' => $this->publisher,
            'price' => (float) $this->price,
            'stock_quantity' => $this->stock_quantity,
            'format' => $this->format,
            'cover_image_url' => $this->cover_image_url,
            'is_featured' => $this->is_featured,
            'published_at' => $this->published_at?->toDateString(),
            'description' => $this->when(
                $request->routeIs('books.show'),
                $this->description
            ),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'average_rating' => $this->when(
                $this->relationLoaded('reviews'),
                $this->average_rating
            ),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
