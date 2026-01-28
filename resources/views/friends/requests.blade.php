@extends('layouts.app')

@section('title', 'Friend Requests')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">Friend Requests</h5>
                </div>
                <div class="card-body">
                    <div id="requestsList">
                        <p class="text-muted text-center">Loading friend requests...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Friend Request Template -->
<template id="requestTemplate">
    <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom request-item" data-friendship-id="">
        <div class="d-flex align-items-center gap-3">
            <img src="" alt="User" class="avatar request-avatar">
            <div>
                <h6 class="mb-0 request-name"></h6>
                <small class="text-muted request-email"></small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary-custom btn-sm request-accept-btn">
                <i class="fas fa-check"></i> Accept
            </button>
            <button class="btn btn-danger btn-sm request-reject-btn">
                <i class="fas fa-times"></i> Reject
            </button>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
// Load Friend Requests
async function loadFriendRequests() {
    try {
        const response = await axios.get('/api/v1/friends/requests');
        
        const container = document.getElementById('requestsList');
        const requests = response.data.data;
        
        if (requests.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">No pending friend requests.</p>';
            return;
        }
        
        container.innerHTML = '';
        requests.forEach(user => {
            const template = document.getElementById('requestTemplate');
            const clone = template.content.cloneNode(true);
            
            const requestDiv = clone.querySelector('.request-item');
            requestDiv.dataset.friendshipId = user.friendship_id;
            
            clone.querySelector('.request-avatar').src = user.profile_picture || `https://ui-avatars.com/api/?name=${user.name}`;
            clone.querySelector('.request-name').textContent = user.name;
            clone.querySelector('.request-email').textContent = user.email;
            clone.querySelector('.request-accept-btn').addEventListener('click', () => acceptRequest(user.friendship_id, requestDiv));
            clone.querySelector('.request-reject-btn').addEventListener('click', () => rejectRequest(user.friendship_id, requestDiv));
            
            container.appendChild(clone);
        });
    } catch (error) {
        console.error('Error loading friend requests:', error);
        document.getElementById('requestsList').innerHTML = '<p class="text-muted text-center">Failed to load requests.</p>';
    }
}

// Accept Friend Request
async function acceptRequest(friendshipId, element) {
    try {
        await axios.post(`/api/v1/friends/${friendshipId}/accept`, {});
        showToast('Friend request accepted!', 'success');
        element.remove();
        
        // Reload if no more requests
        if (document.querySelectorAll('.request-item').length === 0) {
            loadFriendRequests();
        }
    } catch (error) {
        console.error('Error accepting request:', error);
        showToast('Failed to accept request: ' + (error.response?.data?.message || error.message), 'danger');
        loadFriendRequests();
    }
}

// Reject Friend Request
async function rejectRequest(friendshipId, element) {
    if (!confirm('Reject this friend request?')) return;
    
    try {
        await axios.post(`/api/v1/friends/${friendshipId}/reject`, {});
        showToast('Friend request rejected!', 'success');
        element.remove();
        
        // Reload if no more requests
        if (document.querySelectorAll('.request-item').length === 0) {
            loadFriendRequests();
        }
    } catch (error) {
        console.error('Error rejecting request:', error);
        showToast('Failed to reject request: ' + (error.response?.data?.message || error.message), 'danger');
        loadFriendRequests();
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadFriendRequests();
});
</script>
@endpush
