<?php

namespace App\Domain\Friendship\Models;

use App\Domain\Friendship\Enums\FriendshipStatus;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Friendship extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'friend_id',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => FriendshipStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who sent the friend request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who received the friend request.
     */
    public function friend(): BelongsTo
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

    /**
     * Scope a query to only include pending friendships.
     */
    public function scopePending($query)
    {
        return $query->where('status', FriendshipStatus::PENDING);
    }

    /**
     * Scope a query to only include accepted friendships.
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', FriendshipStatus::ACCEPTED);
    }

    /**
     * Scope a query to only include rejected friendships.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', FriendshipStatus::REJECTED);
    }

    /**
     * Accept the friend request.
     */
    public function accept(): bool
    {
        return $this->update(['status' => FriendshipStatus::ACCEPTED]);
    }

    /**
     * Reject the friend request.
     */
    public function reject(): bool
    {
        return $this->update(['status' => FriendshipStatus::REJECTED]);
    }

    /**
     * Check if the friendship is pending.
     */
    public function isPending(): bool
    {
        return $this->status === FriendshipStatus::PENDING;
    }

    /**
     * Check if the friendship is accepted.
     */
    public function isAccepted(): bool
    {
        return $this->status === FriendshipStatus::ACCEPTED;
    }

    /**
     * Check if the friendship is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === FriendshipStatus::REJECTED;
    }
}
