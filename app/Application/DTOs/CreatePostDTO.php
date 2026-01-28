<?php

namespace App\Application\DTOs;

use Illuminate\Http\UploadedFile;

class CreatePostDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly string $content,
        public readonly ?UploadedFile $image = null,
    ) {}

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            content: $data['content'],
            image: $data['image'] ?? null,
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'content' => $this->content,
            'image' => $this->image,
        ];
    }
}
