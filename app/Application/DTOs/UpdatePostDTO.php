<?php

namespace App\Application\DTOs;

use Illuminate\Http\UploadedFile;

class UpdatePostDTO
{
    public function __construct(
        public readonly string $content,
        public readonly ?UploadedFile $image = null,
        public readonly bool $removeImage = false,
    ) {}

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            content: $data['content'],
            image: $data['image'] ?? null,
            removeImage: $data['remove_image'] ?? false,
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return array_filter([
            'content' => $this->content,
            'image' => $this->image,
            'remove_image' => $this->removeImage,
        ], fn ($value) => $value !== null);
    }
}
