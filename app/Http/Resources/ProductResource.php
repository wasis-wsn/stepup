<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $imageUrl = null;
        if ($this->image) {
            if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://') || str_starts_with($this->image, '/')) {
                $imageUrl = $this->image;
            } else {
                $imageUrl = '/storage/' . ltrim($this->image, '/');
            }
        }

        $sizes = $this->sizes;
        if (is_string($sizes)) {
            $decoded = json_decode($sizes, true);
            $sizes = is_array($decoded) ? $decoded : array_map('trim', explode(',', $sizes));
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'detail' => $this->detail,
            'sizes' => $sizes ?: ['39', '40', '41', '42', '43', '44', '45'],
            'color' => $this->color,
            'discount' => (int) ($this->discount ?? 0),
            'discounted_price' => number_format((float) $this->discounted_price, 2, '.', ''),
            'price' => number_format((float) $this->price, 2, '.', ''),
            'image' => $imageUrl,
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ] : null,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
