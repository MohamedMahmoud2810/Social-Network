@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="container">
    <div class="row">
        <!-- Left Sidebar -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="sidebar">
                <div class="card-custom mb-3">
                    <div class="card-body text-center">
                        <img src="{{ auth()->user()->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.auth()->user()->name }}" 
                             alt="Profile" class="avatar avatar-lg mb-3">
                        <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                        <p class="text-muted small mb-3">{{ auth()->user()->email }}</p>
                        <a href="{{ route('profile.show', auth()->id()) }}" class="btn btn-outline-custom btn-sm w-100">
                            View Profile
                        </a>
                    </div>
                </div>

                <div class="card-custom">
                    <div class="card-body p-2">
                        <a href="{{ route('home') }}" class="sidebar-item active">
                            <i class="fas fa-home"></i>
                            <span>News Feed</span>
                        </a>
                        <a href="{{ route('friends.index') }}" class="sidebar-item">
                            <i class="fas fa-user-friends"></i>
                            <span>Friends</span>
                        </a>
                        <a href="{{ route('friends.suggestions') }}" class="sidebar-item">
                            <i class="fas fa-user-plus"></i>
                            <span>Find Friends</span>
                        </a>
                        <a href="{{ route('posts.trending') }}" class="sidebar-item">
                            <i class="fas fa-fire"></i>
                            <span>Trending</span>
                        </a>
                        <a href="{{ route('profile.show', auth()->id()) }}" class="sidebar-item">
                            <i class="fas fa-user"></i>
                            <span>My Profile</span>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="sidebar-item">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Feed -->
        <div class="col-lg-6">
            <!-- Create Post Card -->
            <div class="card-custom mb-4 fade-in">
                <div class="card-body">
                    <div class="d-flex gap-3">
                        <img src="{{ auth()->user()->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.auth()->user()->name }}" 
                             alt="Profile" class="avatar">
                        <div class="flex-grow-1">
                            <textarea class="form-control border-0 bg-light" 
                                      id="postContent" 
                                      rows="3" 
                                      placeholder="What's on your mind, {{ auth()->user()->name }}?"
                                      style="resize: none;"></textarea>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <div class="d-flex gap-3">
                            <button class="btn btn-light btn-sm" onclick="document.getElementById('postImage').click()">
                                <i class="fas fa-image text-success"></i> Photo
                            </button>
                            <input type="file" id="postImage" class="d-none" accept="image/*">
                            <button class="btn btn-light btn-sm">
                                <i class="fas fa-smile text-warning"></i> Feeling
                            </button>
                        </div>
                        <button class="btn btn-primary-custom" onclick="createPost()">
                            <i class="fas fa-paper-plane"></i> Post
                        </button>
                    </div>
                    
                    <div id="imagePreview" class="mt-3" style="display: none;">
                        <img id="previewImg" src="" class="img-fluid rounded" style="max-height: 300px;">
                        <button class="btn btn-sm btn-danger mt-2" onclick="removeImage()">
                            <i class="fas fa-times"></i> Remove
                        </button>
                    </div>
                </div>
            </div>

            <!-- Posts Feed -->
            <div id="postsContainer">
                <!-- Posts will be loaded here dynamically -->
            </div>

            <!-- Loading Indicator -->
            <div class="text-center py-4" id="loadingPosts">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="col-lg-3 d-none d-lg-block">
            <!-- Friend Requests -->
            <div class="card-custom mb-3">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Friend Requests</h6>
                    <a href="{{ route('friends.requests') }}" class="text-primary small">See all</a>
                </div>
                <div class="card-body" id="friendRequests">
                    <!-- Friend requests will be loaded here -->
                </div>
            </div>

            <!-- Suggestions -->
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">People You May Know</h6>
                    <a href="{{ route('friends.suggestions') }}" class="text-primary small">See all</a>
                </div>
                <div class="card-body" id="suggestions">
                    <!-- Suggestions will be loaded here -->
                </div>
            </div>

            <!-- Trending Topics -->
            <div class="card-custom mt-3">
                <div class="card-header-custom">
                    <h6 class="mb-0">Trending Topics</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="text-muted small">Technology</div>
                            <div class="fw-semibold">#LaravelDev</div>
                        </div>
                        <span class="badge bg-primary">125 posts</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="text-muted small">Social</div>
                            <div class="fw-semibold">#FriendshipGoals</div>
                        </div>
                        <span class="badge bg-primary">89 posts</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Trending</div>
                            <div class="fw-semibold">#SocialNetworking</div>
                        </div>
                        <span class="badge bg-primary">234 posts</span>
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
                <div class="dropdown post-menu">
                    <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item edit-post"><i class="fas fa-edit me-2"></i> Edit</a></li>
                        <li><a class="dropdown-item delete-post text-danger"><i class="fas fa-trash me-2"></i> Delete</a></li>
                    </ul>
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
                <button class="btn btn-light">
                    <i class="far fa-share-square"></i> Share
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
                <button class="btn btn-link btn-sm p-0 text-muted delete-comment-btn">Delete</button>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let isLoading = false;
let selectedImage = null;

// Load Posts
async function loadPosts(page = 1) {
    if (isLoading) return;
    isLoading = true;
    
    try {
        const response = await axios.get(`/api/v1/posts?page=${page}`);
        
        const posts = response.data.data;
        const container = document.getElementById('postsContainer');
        
        posts.forEach(post => {
            const postElement = createPostElement(post);
            container.appendChild(postElement);
        });
        
        currentPage = page;
        isLoading = false;
        document.getElementById('loadingPosts').style.display = 'none';
    } catch (error) {
        console.error('Error loading posts:', error);
        isLoading = false;
        document.getElementById('loadingPosts').style.display = 'none';
        showToast('Failed to load posts', 'danger');
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
    clone.querySelector('.edit-post').addEventListener('click', () => editPost(post.id));
    clone.querySelector('.delete-post').addEventListener('click', () => deletePost(post.id));
    
    // Hide menu for other users' posts
    if (post.user.id !== {{ auth()->id() }}) {
        clone.querySelector('.post-menu').style.display = 'none';
    }
    
    return clone;
}

// Create Post
async function createPost() {
    const content = document.getElementById('postContent').value;
    if (!content.trim() && !selectedImage) {
        showToast('Please write something or select an image', 'warning');
        return;
    }
    
    showLoading();
    
    const formData = new FormData();
    formData.append('content', content);
    if (selectedImage) {
        formData.append('image', selectedImage);
    }
    
    try {
        const response = await axios.post('/api/v1/posts', formData);
        
        document.getElementById('postContent').value = '';
        removeImage();
        
        // Add new post to top of feed
        const container = document.getElementById('postsContainer');
        const newPost = createPostElement(response.data.data);
        container.insertBefore(newPost, container.firstChild);
        
        showToast('Post created successfully!', 'success');
        hideLoading();
    } catch (error) {
        console.error('Error creating post:', error);
        showToast('Failed to create post', 'danger');
        hideLoading();
    }
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
    
    // Hide delete button for other users' comments
    if (comment.user.id !== {{ auth()->id() }}) {
        clone.querySelector('.delete-comment-btn').style.display = 'none';
    } else {
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

// Delete Post
async function deletePost(postId) {
    if (!confirm('Delete this post?')) return;
    
    showLoading();
    
    try {
        await axios.delete(`/api/v1/posts/${postId}`);
        
        document.querySelector(`[data-post-id="${postId}"]`).remove();
        showToast('Post deleted!', 'success');
        hideLoading();
    } catch (error) {
        console.error('Error deleting post:', error);
        showToast('Failed to delete post', 'danger');
        hideLoading();
    }
}

// Image Preview
document.getElementById('postImage').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        selectedImage = file;
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

function removeImage() {
    selectedImage = null;
    document.getElementById('postImage').value = '';
    document.getElementById('imagePreview').style.display = 'none';
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

// Load friend requests and suggestions
async function loadFriendRequests() {
    try {
        const response = await axios.get('/api/v1/friends/requests');
        
        const container = document.getElementById('friendRequests');
        container.innerHTML = '';
        
        response.data.data.slice(0, 3).forEach(user => {
            container.innerHTML += `
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <img src="${user.profile_picture || 'https://ui-avatars.com/api/?name='+user.name}" 
                             class="avatar avatar-sm">
                        <div>
                            <div class="fw-semibold small">${user.name}</div>
                        </div>
                    </div>
                    <button class="btn btn-primary-custom btn-sm" onclick="acceptFriend(${user.id})">
                        <i class="fas fa-check"></i>
                    </button>
                </div>
            `;
        });
        
        if (response.data.data.length === 0) {
            container.innerHTML = '<p class="text-muted small text-center">No friend requests</p>';
        }
    } catch (error) {
        console.error('Error loading friend requests:', error);
    }
}

async function loadSuggestions() {
    try {
        const response = await axios.get('/api/v1/users/suggestions?limit=5');
        
        const container = document.getElementById('suggestions');
        container.innerHTML = '';
        
        response.data.data.forEach(user => {
            container.innerHTML += `
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <img src="${user.profile_picture || 'https://ui-avatars.com/api/?name='+user.name}" 
                             class="avatar avatar-sm">
                        <div>
                            <div class="fw-semibold small">${user.name}</div>
                        </div>
                    </div>
                    <button class="btn btn-outline-custom btn-sm" onclick="sendFriendRequest(${user.id})">
                        <i class="fas fa-user-plus"></i>
                    </button>
                </div>
            `;
        });
    } catch (error) {
        console.error('Error loading suggestions:', error);
    }
}

// Send Friend Request
async function sendFriendRequest(userId) {
        console.log('sendFriendRequest called with userId:', userId);
    try {
        await axios.post('/api/v1/friends/request', { friend_id: userId });
        showToast('Friend request sent!', 'success');
        loadSuggestions();
    } catch (error) {
        console.error('Error sending friend request:', error);
        showToast('Failed to send friend request: ' + (error.response?.data?.message || error.message), 'danger');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadPosts();
    loadFriendRequests();
    loadSuggestions();
    
    // Infinite scroll
    window.addEventListener('scroll', function() {
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
            if (!isLoading) {
                loadPosts(currentPage + 1);
            }
        }
    });
});
</script>
@endpush