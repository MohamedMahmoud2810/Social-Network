@extends('layouts.app')

@section('title', 'Friends')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">My Friends</h5>
                </div>
                <div class="card-body">
                    <div id="friendsList">
                        <p class="text-muted text-center">Loading friends...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Friend Item Template -->
<template id="friendTemplate">
    <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom friend-item">
        <div class="d-flex align-items-center gap-3">
            <img src="" alt="User" class="avatar friend-avatar">
            <div>
                <h6 class="mb-0 friend-name"></h6>
                <small class="text-muted friend-email"></small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="" class="btn btn-primary-custom btn-sm friend-profile-btn">
                <i class="fas fa-user"></i> View Profile
            </a>
            <button class="btn btn-danger btn-sm friend-remove-btn">
                <i class="fas fa-user-minus"></i> Remove
            </button>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
// Load Friends
async function loadFriends() {
    try {
        const response = await axios.get('/api/v1/friends');
        
        const container = document.getElementById('friendsList');
        const friends = response.data.data;
        
        if (friends.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">You have no friends yet.</p>';
            return;
        }
        
        container.innerHTML = '';
        friends.forEach(friend => {
            const template = document.getElementById('friendTemplate');
            const clone = template.content.cloneNode(true);
            
            clone.querySelector('.friend-avatar').src = friend.profile_picture || `https://ui-avatars.com/api/?name=${friend.name}`;
            clone.querySelector('.friend-name').textContent = friend.name;
            clone.querySelector('.friend-email').textContent = friend.email;
            clone.querySelector('.friend-profile-btn').href = `/profile/${friend.id}`;
            clone.querySelector('.friend-remove-btn').addEventListener('click', () => removeFriend(friend.id));
            
            container.appendChild(clone);
        });
    } catch (error) {
        console.error('Error loading friends:', error);
        document.getElementById('friendsList').innerHTML = '<p class="text-muted text-center">Failed to load friends.</p>';
    }
}

// Remove Friend
async function removeFriend(userId) {
    if (!confirm('Remove this friend?')) return;
    
    try {
        await axios.delete(`/api/v1/friends/${userId}`);
        showToast('Friend removed!', 'success');
        loadFriends();
    } catch (error) {
        console.error('Error removing friend:', error);
        showToast('Failed to remove friend', 'danger');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadFriends();
});
</script>
@endpush
