<?php

namespace App\Presentation\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'image' => $this->image_url,
            'user' => new UserResource($this->whenLoaded('user')),
            'likes_count' => $this->likes()->count(),
            'comments_count' => $this->comments()->count(),
            'is_liked' => $request->user() ? $this->isLikedBy($request->user()) : false,
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'likes' => UserResource::collection($this->whenLoaded('likes', function () {
                return $this->likes->pluck('user');
            })),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
