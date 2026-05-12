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
            'price' => (float)$this->price,
            'format' => $this->format,
            'stock_quantity' => $this->stock_quantity,
            'cover_image' => $this->cover_image,
            'published_at' => $this->published_at?->format('Y-m-d'),
            'publication_year' => $this->publication_year,
            'is_active' => $this->is_active,
            'description' => $this->when(
                $request->routeIs('books.show'),
                $this->description
            ),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'average_rating' => $this->when($this->relationLoaded('reviews'), fn() => $this->average_rating),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
