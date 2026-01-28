@extends('layouts.app')

@section('title', 'Friend Suggestions')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">People You May Know</h5>
                </div>
                <div class="card-body">
                    <div id="suggestionsList">
                        <p class="text-muted text-center">Loading suggestions...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Suggestion Template -->
<template id="suggestionTemplate">
    <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom suggestion-item">
        <div class="d-flex align-items-center gap-3">
            <img src="" alt="User" class="avatar suggestion-avatar">
            <div>
                <h6 class="mb-0 suggestion-name"></h6>
                <small class="text-muted suggestion-email"></small>
            </div>
        </div>
        <button class="btn btn-primary-custom btn-sm suggestion-add-btn">
            <i class="fas fa-user-plus"></i> Add Friend
        </button>
    </div>
</template>

@endsection

@push('scripts')
<script>
// Load Friend Suggestions
async function loadSuggestions() {
    try {
        const response = await axios.get('/api/v1/users/suggestions?limit=10');
        
        const container = document.getElementById('suggestionsList');
        const suggestions = response.data.data;
        
        if (suggestions.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">No suggestions available right now.</p>';
            return;
        }
        
        container.innerHTML = '';
        suggestions.forEach(user => {
            const template = document.getElementById('suggestionTemplate');
            const clone = template.content.cloneNode(true);
            
            clone.querySelector('.suggestion-avatar').src = user.profile_picture || `https://ui-avatars.com/api/?name=${user.name}`;
            clone.querySelector('.suggestion-name').textContent = user.name;
            clone.querySelector('.suggestion-email').textContent = user.email;
            clone.querySelector('.suggestion-add-btn').addEventListener('click', () => sendFriendRequest(user.id));
            
            container.appendChild(clone);
        });
    } catch (error) {
        console.error('Error loading suggestions:', error);
        document.getElementById('suggestionsList').innerHTML = '<p class="text-muted text-center">Failed to load suggestions.</p>';
    }
}

// Send Friend Request
async function sendFriendRequest(userId) {
    try {
        await axios.post('/api/v1/friends/request', { user_id: userId });
        showToast('Friend request sent!', 'success');
        loadSuggestions();
    } catch (error) {
        console.error('Error sending friend request:', error);
        showToast('Failed to send friend request', 'danger');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadSuggestions();
});
</script>
@endpush
