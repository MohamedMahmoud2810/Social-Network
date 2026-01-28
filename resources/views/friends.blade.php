@extends('layouts.app')

@section('title', 'Friends')

@section('content')
<div class="container">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="card-custom sticky-top" style="top: 80px;">
                <div class="card-header-custom">
                    <h5 class="mb-0">Friends</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#all" class="list-group-item list-group-item-action active" onclick="showTab('all', event)">
                        <i class="fas fa-user-friends me-2"></i> All Friends
                        <span class="badge bg-primary float-end" id="allCount">0</span>
                    </a>
                    <a href="#requests" class="list-group-item list-group-item-action" onclick="showTab('requests', event)">
                        <i class="fas fa-user-clock me-2"></i> Requests
                        <span class="badge bg-danger float-end" id="requestsCount">0</span>
                    </a>
                    <a href="#pending" class="list-group-item list-group-item-action" onclick="showTab('pending', event)">
                        <i class="fas fa-paper-plane me-2"></i> Sent Requests
                        <span class="badge bg-warning float-end" id="pendingCount">0</span>
                    </a>
                    <a href="#suggestions" class="list-group-item list-group-item-action" onclick="showTab('suggestions', event)">
                        <i class="fas fa-user-plus me-2"></i> Suggestions
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- All Friends Tab -->
            <div id="all-tab" class="tab-content-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3><i class="fas fa-user-friends text-primary"></i> My Friends</h3>
                    <div class="input-group" style="max-width: 300px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Search friends..." id="searchFriends" oninput="filterFriends()">
                    </div>
                </div>

                <div class="row g-3" id="friendsGrid">
                    <!-- Friends will be loaded here -->
                </div>

                <div id="noFriendsMessage" class="text-center py-5" style="display: none;">
                    <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No friends yet</h5>
                    <p class="text-muted">Start connecting with people!</p>
                    <button class="btn btn-primary-custom" onclick="showTab('suggestions')">
                        Find Friends
                    </button>
                </div>
            </div>

            <!-- Friend Requests Tab -->
            <div id="requests-tab" class="tab-content-section" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3><i class="fas fa-user-clock text-primary"></i> Friend Requests</h3>
                </div>

                <div class="row g-3" id="requestsGrid">
                    <!-- Friend requests will be loaded here -->
                </div>

                <div id="noRequestsMessage" class="text-center py-5" style="display: none;">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No pending requests</h5>
                    <p class="text-muted">You're all caught up!</p>
                </div>
            </div>

            <!-- Sent Requests Tab -->
            <div id="pending-tab" class="tab-content-section" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3><i class="fas fa-paper-plane text-primary"></i> Sent Requests</h3>
                </div>

                <div class="row g-3" id="pendingGrid">
                    <!-- Pending requests will be loaded here -->
                </div>

                <div id="noPendingMessage" class="text-center py-5" style="display: none;">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No sent requests</h5>
                    <p class="text-muted">Send friend requests to connect with people!</p>
                </div>
            </div>

            <!-- Suggestions Tab -->
            <div id="suggestions-tab" class="tab-content-section" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3><i class="fas fa-user-plus text-primary"></i> People You May Know</h3>
                    <button class="btn btn-outline-custom btn-sm" onclick="loadSuggestions()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>

                <div class="row g-3" id="suggestionsGrid">
                    <!-- Suggestions will be loaded here -->
                </div>

                <div id="noSuggestionsMessage" class="text-center py-5" style="display: none;">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No suggestions available</h5>
                    <p class="text-muted">Check back later for new suggestions!</p>
                </div>
            </div>

            <!-- Loading -->
            <div class="text-center py-5" id="loading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Friend Card Template -->
<template id="friendCardTemplate">
    <div class="col-md-6 col-lg-4 friend-card">
        <div class="card-custom h-100">
            <div class="card-body text-center">
                <div class="position-relative d-inline-block mb-3">
                    <img src="" alt="Profile" class="avatar avatar-lg friend-avatar">
                    <span class="position-absolute bottom-0 end-0 bg-success border border-3 border-white rounded-circle" style="width: 20px; height: 20px;"></span>
                </div>
                <h5 class="mb-1 friend-name"></h5>
                <p class="text-muted small mb-3 friend-email"></p>
                <div class="friend-actions">
                    <!-- Actions will be inserted here -->
                </div>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
let allFriends = [];

// Show Tab
function showTab(tab, event) {
    if (event) {
        event.preventDefault();
        document.querySelectorAll('.list-group-item').forEach(item => {
            item.classList.remove('active');
        });
        event.target.classList.add('active');
    }
    
    document.querySelectorAll('.tab-content-section').forEach(section => {
        section.style.display = 'none';
    });
    
    document.getElementById(tab + '-tab').style.display = 'block';
    
    switch(tab) {
        case 'all':
            loadFriends();
            break;
        case 'requests':
            loadFriendRequests();
            break;
        case 'pending':
            loadPendingRequests();
            break;
        case 'suggestions':
            loadSuggestions();
            break;
    }
}

// Load All Friends
async function loadFriends() {
    document.getElementById('loading').style.display = 'block';
    
    try {
        const response = await axios.get('/api/v1/friends', {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });
        
        allFriends = response.data.data;
        document.getElementById('allCount').textContent = allFriends.length;
        
        displayFriends(allFriends);
        
        document.getElementById('loading').style.display = 'none';
    } catch (error) {
        console.error('Error loading friends:', error);
        document.getElementById('loading').style.display = 'none';
    }
}

// Display Friends
function displayFriends(friends) {
    const grid = document.getElementById('friendsGrid');
    grid.innerHTML = '';
    
    if (friends.length === 0) {
        document.getElementById('noFriendsMessage').style.display = 'block';
        return;
    }
    
    document.getElementById('noFriendsMessage').style.display = 'none';
    
    friends.forEach(friend => {
        const card = createFriendCard(friend, 'friend');
        grid.appendChild(card);
    });
}

// Filter Friends
function filterFriends() {
    const searchTerm = document.getElementById('searchFriends').value.toLowerCase();
    const filtered = allFriends.filter(friend => 
        friend.name.toLowerCase().includes(searchTerm) ||
        friend.email.toLowerCase().includes(searchTerm)
    );
    displayFriends(filtered);
}

// Load Friend Requests
async function loadFriendRequests() {
    document.getElementById('loading').style.display = 'block';
    
    try {
        const response = await axios.get('/api/v1/friends/requests', {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });
        
        const requests = response.data.data;
        document.getElementById('requestsCount').textContent = requests.length;
        
        const grid = document.getElementById('requestsGrid');
        grid.innerHTML = '';
        
        if (requests.length === 0) {
            document.getElementById('noRequestsMessage').style.display = 'block';
        } else {
            document.getElementById('noRequestsMessage').style.display = 'none';
            requests.forEach(user => {
                const card = createFriendCard(user, 'request');
                grid.appendChild(card);
            });
        }
        
        document.getElementById('loading').style.display = 'none';
    } catch (error) {
        console.error('Error loading friend requests:', error);
        document.getElementById('loading').style.display = 'none';
    }
}

// Load Pending Requests
async function loadPendingRequests() {
    document.getElementById('loading').style.display = 'block';
    
    try {
        const response = await axios.get('/api/v1/friends/pending', {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });
        
        const pending = response.data.data;
        document.getElementById('pendingCount').textContent = pending.length;
        
        const grid = document.getElementById('pendingGrid');
        grid.innerHTML = '';
        
        if (pending.length === 0) {
            document.getElementById('noPendingMessage').style.display = 'block';
        } else {
            document.getElementById('noPendingMessage').style.display = 'none';
            pending.forEach(user => {
                const card = createFriendCard(user, 'pending');
                grid.appendChild(card);
            });
        }
        
        document.getElementById('loading').style.display = 'none';
    } catch (error) {
        console.error('Error loading pending requests:', error);
        document.getElementById('loading').style.display = 'none';
    }
}

// Load Suggestions
async function loadSuggestions() {
    document.getElementById('loading').style.display = 'block';
    
    try {
        const response = await axios.get('/api/v1/users/suggestions?limit=12', {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });
        
        const suggestions = response.data.data;
        
        const grid = document.getElementById('suggestionsGrid');
        grid.innerHTML = '';
        
        if (suggestions.length === 0) {
            document.getElementById('noSuggestionsMessage').style.display = 'block';
        } else {
            document.getElementById('noSuggestionsMessage').style.display = 'none';
            suggestions.forEach(user => {
                const card = createFriendCard(user, 'suggestion');
                grid.appendChild(card);
            });
        }
        
        document.getElementById('loading').style.display = 'none';
    } catch (error) {
        console.error('Error loading suggestions:', error);
        document.getElementById('loading').style.display = 'none';
    }
}

// Create Friend Card
function createFriendCard(user, type) {
    const template = document.getElementById('friendCardTemplate');
    const clone = template.content.cloneNode(true);
    
    clone.querySelector('.friend-avatar').src = user.profile_picture || `https://ui-avatars.com/api/?name=${user.name}&size=200`;
    clone.querySelector('.friend-name').textContent = user.name;
    clone.querySelector('.friend-email').textContent = user.email;
    
    const actionsDiv = clone.querySelector('.friend-actions');
    
    if (type === 'friend') {
        actionsDiv.innerHTML = `
            <a href="/profile/${user.id}" class="btn btn-primary-custom btn-sm w-100 mb-2">
                <i class="fas fa-user"></i> View Profile
            </a>
            <button class="btn btn-outline-danger btn-sm w-100" onclick="unfriend(${user.id}, this)">
                <i class="fas fa-user-times"></i> Unfriend
            </button>
        `;
    } else if (type === 'request') {
        actionsDiv.innerHTML = `
            <button class="btn btn-success btn-sm w-100 mb-2" onclick="acceptFriendRequest(${user.id}, this)">
                <i class="fas fa-check"></i> Accept
            </button>
            <button class="btn btn-outline-danger btn-sm w-100" onclick="rejectFriendRequest(${user.id}, this)">
                <i class="fas fa-times"></i> Decline
            </button>
        `;
    } else if (type === 'pending') {
        actionsDiv.innerHTML = `
            <button class="btn btn-secondary btn-sm w-100" disabled>
                <i class="fas fa-clock"></i> Pending
            </button>
        `;
    } else if (type === 'suggestion') {
        actionsDiv.innerHTML = `
            <button class="btn btn-primary-custom btn-sm w-100 mb-2" onclick="sendFriendRequest(${user.id}, this)">
                <i class="fas fa-user-plus"></i> Add Friend
            </button>
            <a href="/profile/${user.id}" class="btn btn-outline-custom btn-sm w-100">
                <i class="fas fa-eye"></i> View Profile
            </a>
        `;
    }
    
    return clone;
}

// Send Friend Request
async function sendFriendRequest(userId, button) {
    button.disabled = true;
    
    try {
        await axios.post('/api/v1/friends/request', 
            { friend_id: userId },
            { headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` } }
        );
        
        button.innerHTML = '<i class="fas fa-clock"></i> Request Sent';
        button.classList.remove('btn-primary-custom');
        button.classList.add('btn-secondary');
        
        showToast('Friend request sent!', 'success');
    } catch (error) {
        console.error('Error sending friend request:', error);
        showToast('Failed to send request', 'danger');
        button.disabled = false;
    }
}

// Accept Friend Request
async function acceptFriendRequest(userId, button) {
    button.disabled = true;
    
    try {
        // Note: Need friendship ID, this is simplified
        await axios.post(`/api/v1/friends/${userId}/accept`, {}, {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });
        
        button.closest('.friend-card').remove();
        showToast('Friend request accepted!', 'success');
        
        // Update counts
        loadFriendRequests();
        loadFriends();
    } catch (error) {
        console.error('Error accepting friend request:', error);
        showToast('Failed to accept request', 'danger');
        button.disabled = false;
    }
}

// Reject Friend Request
async function rejectFriendRequest(userId, button) {
    if (!confirm('Reject this friend request?')) return;
    
    button.disabled = true;
    
    try {
        await axios.post(`/api/v1/friends/${userId}/reject`, {}, {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });
        
        button.closest('.friend-card').remove();
        showToast('Friend request rejected', 'success');
        
        loadFriendRequests();
    } catch (error) {
        console.error('Error rejecting friend request:', error);
        showToast('Failed to reject request', 'danger');
        button.disabled = false;
    }
}

// Unfriend
async function unfriend(userId, button) {
    if (!confirm('Are you sure you want to unfriend this person?')) return;
    
    button.disabled = true;
    
    try {
        await axios.delete(`/api/v1/friends/${userId}`, {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });
        
        button.closest('.friend-card').remove();
        showToast('Unfriended successfully', 'success');
        
        loadFriends();
    } catch (error) {
        console.error('Error unfriending:', error);
        showToast('Failed to unfriend', 'danger');
        button.disabled = false;
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadFriends();
    loadFriendRequests();
    loadPendingRequests();
});
</script>
@endpush