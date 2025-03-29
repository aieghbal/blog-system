<?php

namespace YourVendor\BlogSystem\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'cover_image' => $this->getCoverImageUrlAttribute(),
            'status' => $this->status,
            'category' => new CategoryResource($this->category),
            'tags' => TagResource::collection($this->tags),
            'meta' => [
                'title' => $this->meta_title,
                'description' => $this->meta_description,
                'keywords' => $this->meta_keywords,
            ]
        ];
    }
}
