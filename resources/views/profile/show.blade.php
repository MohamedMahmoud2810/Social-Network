@extends('layouts.app')

@section('title', $user->name . ' - Profile')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Profile Header -->
            <div class="card card-custom mb-4">
                <div class="card-body text-center py-5">
                    <img src="{{ $user->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.$user->name }}" 
                         alt="{{ $user->name }}" class="avatar avatar-lg mb-4">
                    <h3 class="mb-2">{{ $user->name }}</h3>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    <p class="mb-4">{{ $user->bio }}</p>
                    
                    @if(auth()->id() === $user->id)
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary-custom me-2">
                            <i class="fas fa-edit"></i> Edit Profile
                        </a>
                    @else
                        <button class="btn btn-primary-custom me-2" id="friendActionBtn" onclick="performFriendAction()">
                            <i class="fas fa-user-plus"></i> <span id="friendActionText">Add Friend</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Posts Section -->
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">Posts</h5>
                </div>
                <div class="card-body">
                    <div id="userPostsContainer">
                        <p class="text-muted text-center">Loading posts...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Post Template -->
<template id="postTemplate">
    <div class="card-custom mb-4 fade-in post-card" data-post-id="">
        <div class="card-body">
            <!-- Post Header -->
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex gap-3">
                    <img src="" alt="User" class="avatar post-user-avatar">
                    <div>
                        <h6 class="mb-0 post-user-name"></h6>
                        <small class="text-muted post-time"></small>
                    </div>
                </div>
            </div>

            <!-- Post Content -->
            <div class="post-content mb-3"></div>
            
            <!-- Post Image -->
            <div class="post-image mb-3" style="display: none;">
                <img src="" class="img-fluid rounded w-100" style="max-height: 500px; object-fit: cover;">
            </div>

            <!-- Post Stats -->
            <div class="d-flex justify-content-between align-items-center py-2 border-top border-bottom">
                <div class="text-muted small">
                    <i class="fas fa-heart text-danger"></i> 
                    <span class="likes-count">0</span> likes
                </div>
                <div class="text-muted small">
                    <span class="comments-count">0</span> comments
                </div>
            </div>

            <!-- Post Actions -->
            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-light flex-grow-1 like-btn">
                    <i class="far fa-heart"></i> Like
                </button>
                <button class="btn btn-light flex-grow-1 comment-btn">
                    <i class="far fa-comment"></i> Comment
                </button>
            </div>

            <!-- Comments Section -->
            <div class="comments-section mt-3" style="display: none;">
                <div class="comments-list mb-3"></div>
                
                <!-- Add Comment -->
                <div class="d-flex gap-2">
                    <img src="{{ auth()->user()->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.auth()->user()->name }}" 
                         alt="Profile" class="avatar avatar-sm">
                    <input type="text" class="form-control comment-input" placeholder="Write a comment...">
                    <button class="btn btn-primary-custom post-comment-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Comment Template -->
<template id="commentTemplate">
    <div class="d-flex gap-2 mb-3 comment-item" data-comment-id="">
        <img src="" alt="User" class="avatar avatar-sm comment-user-avatar">
        <div class="flex-grow-1">
            <div class="bg-light rounded p-2">
                <h6 class="mb-0 small comment-user-name"></h6>
                <p class="mb-0 small comment-content"></p>
            </div>
            <div class="d-flex gap-3 mt-1">
                <small class="text-muted comment-time"></small>
                <button class="btn btn-link btn-sm p-0 text-muted delete-comment-btn" style="display: none;">Delete</button>
            </div>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
const userId = {{ $user->id }};
const currentUserId = {{ auth()->id() }};

// Load user posts
async function loadUserPosts() {
    try {
        const response = await axios.get(`/api/v1/users/${userId}/posts`);
        
        const container = document.getElementById('userPostsContainer');
        const posts = response.data.data;
        
        if (posts.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">No posts yet.</p>';
            return;
        }
        
        container.innerHTML = '';
        posts.forEach(post => {
            const postElement = createPostElement(post);
            container.appendChild(postElement);
        });
    } catch (error) {
        console.error('Error loading posts:', error);
        document.getElementById('userPostsContainer').innerHTML = '<p class="text-muted text-center">Failed to load posts.</p>';
    }
}

// Create Post Element
function createPostElement(post) {
    const template = document.getElementById('postTemplate');
    const clone = template.content.cloneNode(true);
    
    const postCard = clone.querySelector('.post-card');
    postCard.dataset.postId = post.id;
    
    // Set user info
    clone.querySelector('.post-user-avatar').src = post.user.profile_picture || `https://ui-avatars.com/api/?name=${post.user.name}`;
    clone.querySelector('.post-user-name').textContent = post.user.name;
    clone.querySelector('.post-time').textContent = formatTime(post.created_at);
    
    // Set content
    clone.querySelector('.post-content').textContent = post.content;
    
    // Set image if exists
    if (post.image) {
        const imageDiv = clone.querySelector('.post-image');
        imageDiv.style.display = 'block';
        imageDiv.querySelector('img').src = post.image;
    }
    
    // Set stats
    clone.querySelector('.likes-count').textContent = post.likes_count;
    clone.querySelector('.comments-count').textContent = post.comments_count;
    
    // Set like button state
    const likeBtn = clone.querySelector('.like-btn');
    if (post.is_liked) {
        likeBtn.innerHTML = '<i class="fas fa-heart text-danger"></i> Liked';
        likeBtn.classList.add('text-danger');
    }
    
    // Add event listeners
    likeBtn.addEventListener('click', () => toggleLike(post.id));
    clone.querySelector('.comment-btn').addEventListener('click', () => toggleComments(post.id));
    clone.querySelector('.post-comment-btn').addEventListener('click', () => addComment(post.id));
    
    return clone;
}

// Toggle Like
async function toggleLike(postId) {
    const postCard = document.querySelector(`[data-post-id="${postId}"]`);
    const likeBtn = postCard.querySelector('.like-btn');
    const likesCount = postCard.querySelector('.likes-count');
    const isLiked = likeBtn.classList.contains('text-danger');
    
    try {
        if (isLiked) {
            await axios.delete(`/api/v1/posts/${postId}/unlike`);
            likeBtn.innerHTML = '<i class="far fa-heart"></i> Like';
            likeBtn.classList.remove('text-danger');
            likesCount.textContent = parseInt(likesCount.textContent) - 1;
        } else {
            await axios.post(`/api/v1/posts/${postId}/like`, {});
            likeBtn.innerHTML = '<i class="fas fa-heart text-danger"></i> Liked';
            likeBtn.classList.add('text-danger');
            likesCount.textContent = parseInt(likesCount.textContent) + 1;
        }
    } catch (error) {
        console.error('Error toggling like:', error);
        showToast('Failed to update like', 'danger');
    }
}

// Toggle Comments
async function toggleComments(postId) {
    const postCard = document.querySelector(`[data-post-id="${postId}"]`);
    const commentsSection = postCard.querySelector('.comments-section');
    
    if (commentsSection.style.display === 'none') {
        commentsSection.style.display = 'block';
        await loadComments(postId);
    } else {
        commentsSection.style.display = 'none';
    }
}

// Load Comments
async function loadComments(postId) {
    const postCard = document.querySelector(`[data-post-id="${postId}"]`);
    const commentsList = postCard.querySelector('.comments-list');
    
    try {
        const response = await axios.get(`/api/v1/posts/${postId}/comments`);
        
        commentsList.innerHTML = '';
        response.data.data.forEach(comment => {
            const commentElement = createCommentElement(comment);
            commentsList.appendChild(commentElement);
        });
    } catch (error) {
        console.error('Error loading comments:', error);
    }
}

// Create Comment Element
function createCommentElement(comment) {
    const template = document.getElementById('commentTemplate');
    const clone = template.content.cloneNode(true);
    
    const commentDiv = clone.querySelector('.comment-item');
    commentDiv.dataset.commentId = comment.id;
    
    clone.querySelector('.comment-user-avatar').src = comment.user.profile_picture || `https://ui-avatars.com/api/?name=${comment.user.name}`;
    clone.querySelector('.comment-user-name').textContent = comment.user.name;
    clone.querySelector('.comment-content').textContent = comment.content;
    clone.querySelector('.comment-time').textContent = formatTime(comment.created_at);
    
    // Show delete button only for own comments
    if (comment.user.id === currentUserId) {
        clone.querySelector('.delete-comment-btn').style.display = 'inline';
        clone.querySelector('.delete-comment-btn').addEventListener('click', () => deleteComment(comment.id));
    }
    
    return clone;
}

// Add Comment
async function addComment(postId) {
    const postCard = document.querySelector(`[data-post-id="${postId}"]`);
    const commentInput = postCard.querySelector('.comment-input');
    const content = commentInput.value.trim();
    
    if (!content) return;
    
    try {
        const response = await axios.post(`/api/v1/posts/${postId}/comments`, { content });
        
        const commentsList = postCard.querySelector('.comments-list');
        const newComment = createCommentElement(response.data.data);
        commentsList.appendChild(newComment);
        
        commentInput.value = '';
        
        // Update comments count
        const commentsCount = postCard.querySelector('.comments-count');
        commentsCount.textContent = parseInt(commentsCount.textContent) + 1;
        
        showToast('Comment added!', 'success');
    } catch (error) {
        console.error('Error adding comment:', error);
        showToast('Failed to add comment', 'danger');
    }
}

// Delete Comment
async function deleteComment(commentId) {
    if (!confirm('Delete this comment?')) return;
    
    try {
        await axios.delete(`/api/v1/comments/${commentId}`);
        
        document.querySelector(`[data-comment-id="${commentId}"]`).remove();
        showToast('Comment deleted!', 'success');
    } catch (error) {
        console.error('Error deleting comment:', error);
        showToast('Failed to delete comment', 'danger');
    }
}

// Format Time
function formatTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);
    
    if (diff < 60) return 'Just now';
    if (diff < 3600) return Math.floor(diff / 60) + ' minutes ago';
    if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
    if (diff < 604800) return Math.floor(diff / 86400) + ' days ago';
    
    return date.toLocaleDateString();
}

// Refresh the friend action button state
async function refreshFriendButton() {
    try {
        const response = await axios.get(`/api/v1/friends/status/${userId}`);
        const status = response.data.data?.status || response.data.status || 'none';
        const btn = document.getElementById('friendActionBtn');
        const text = document.getElementById('friendActionText');

        btn.dataset.status = status;

        if (status === 'friends') {
            btn.innerHTML = '<i class="fas fa-user-check"></i> <span id="friendActionText">Friends</span>';
        } else if (status === 'pending_sent') {
            btn.innerHTML = '<i class="fas fa-clock"></i> <span id="friendActionText">Request Sent</span>';
        } else if (status === 'pending_received') {
            btn.innerHTML = '<i class="fas fa-user-clock"></i> <span id="friendActionText">Respond</span>';
        } else {
            btn.innerHTML = '<i class="fas fa-user-plus"></i> <span id="friendActionText">Add Friend</span>';
        }
    } catch (error) {
        console.error('Error fetching friendship status:', error);
    }
}

// Perform the appropriate friend action based on current status
async function performFriendAction() {
    const btn = document.getElementById('friendActionBtn');
    const status = btn.dataset.status || 'none';

    try {
        if (status === 'friends') {
            if (!confirm('Remove this friend?')) return;
            await axios.delete(`/api/v1/friends/${userId}`);
            showToast('Friend removed!', 'success');
        } else if (status === 'pending_sent') {
            if (!confirm('Cancel your friend request?')) return;
            await axios.delete(`/api/v1/friends/${userId}`);
            showToast('Friend request cancelled!', 'success');
        } else if (status === 'pending_received') {
            // For received requests, redirect to requests page
            window.location.href = '{{ route('friends.requests') }}';
            return;
        } else {
            // Send friend request
            await axios.post('/api/v1/friends/request', { friend_id: userId });
            showToast('Friend request sent!', 'success');
        }

        // Refresh button state after action
        await refreshFriendButton();
    } catch (error) {
        console.error('Error performing friend action:', error);
        showToast('Failed to perform action', 'danger');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadUserPosts();
    
    // If not own profile, check friendship status
    if (currentUserId !== userId) {
        refreshFriendButton();
    }
});
</script>
@endpush
