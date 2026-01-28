<?php

namespace App\Application\DTOs;

use Illuminate\Http\UploadedFile;

class UpdateProfileDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $bio = null,
        public readonly ?UploadedFile $profilePicture = null,
        public readonly bool $removeProfilePicture = false,
    ) {}

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            bio: $data['bio'] ?? null,
            profilePicture: $data['profile_picture'] ?? null,
            removeProfilePicture: $data['remove_profile_picture'] ?? false,
        );
    }

    /**
     * Convert to array for database update.
     */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'email' => $this->email,
            'bio' => $this->bio,
        ], fn ($value) => $value !== null);
    }
}
