@extends('layouts.app')

@section('title', 'Pending Requests')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">Pending Friend Requests</h5>
                </div>
                <div class="card-body">
                    <div id="pendingList">
                        <p class="text-muted text-center">Loading pending requests...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Request Template -->
<template id="pendingTemplate">
    <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom pending-item">
        <div class="d-flex align-items-center gap-3">
            <img src="" alt="User" class="avatar pending-avatar">
            <div>
                <h6 class="mb-0 pending-name"></h6>
                <small class="text-muted pending-email"></small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary-custom btn-sm pending-accept-btn">
                <i class="fas fa-check"></i> Accept
            </button>
            <button class="btn btn-danger btn-sm pending-reject-btn">
                <i class="fas fa-times"></i> Reject
            </button>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
// Load Pending Requests
async function loadPendingRequests() {
    try {
        const response = await axios.get('/api/v1/friends/pending');
        
        const container = document.getElementById('pendingList');
        const pending = response.data.data;
        
        if (pending.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">No pending friend requests.</p>';
            return;
        }
        
        container.innerHTML = '';
        pending.forEach(request => {
            const template = document.getElementById('pendingTemplate');
            const clone = template.content.cloneNode(true);
            
            clone.querySelector('.pending-avatar').src = request.profile_picture || `https://ui-avatars.com/api/?name=${request.name}`;
            clone.querySelector('.pending-name').textContent = request.name;
            clone.querySelector('.pending-email').textContent = request.email;
            clone.querySelector('.pending-accept-btn').addEventListener('click', () => acceptFriendRequest(request.id));
            clone.querySelector('.pending-reject-btn').addEventListener('click', () => rejectFriendRequest(request.id));
            
            container.appendChild(clone);
        });
    } catch (error) {
        console.error('Error loading pending requests:', error);
        document.getElementById('pendingList').innerHTML = '<p class="text-muted text-center">Failed to load pending requests.</p>';
    }
}

// Accept Friend Request
async function acceptFriendRequest(userId) {
    try {
        await axios.post(`/api/v1/friends/${userId}/accept`, {});
        showToast('Friend request accepted!', 'success');
        loadPendingRequests();
    } catch (error) {
        console.error('Error accepting request:', error);
        showToast('Failed to accept request', 'danger');
    }
}

// Reject Friend Request
async function rejectFriendRequest(userId) {
    if (!confirm('Reject this friend request?')) return;
    
    try {
        await axios.post(`/api/v1/friends/${userId}/reject`, {});
        showToast('Friend request rejected!', 'success');
        loadPendingRequests();
    } catch (error) {
        console.error('Error rejecting request:', error);
        showToast('Failed to reject request', 'danger');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadPendingRequests();
});
</script>
@endpush
