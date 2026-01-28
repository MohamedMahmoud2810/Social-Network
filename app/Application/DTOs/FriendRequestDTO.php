<?php

namespace App\Application\DTOs;

class FriendRequestDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly int $friendId,
    ) {}

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            friendId: $data['friend_id'],
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'friend_id' => $this->friendId,
        ];
    }
}
