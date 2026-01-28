@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="container">
    <!-- Profile Header -->
    <div class="card-custom mb-4 overflow-hidden">
        <!-- Cover Photo -->
        <div class="position-relative" style="height: 300px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));">
            <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(to top, rgba(0,0,0,0.5), transparent);">
                <div class="d-flex align-items-end gap-4">
                    <!-- Profile Picture -->
                    <div class="position-relative">
                        <img src="{{ $user->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.$user->name.'&size=200' }}" 
                             alt="Profile" 
                             class="rounded-circle border border-4 border-white"
                             style="width: 150px; height: 150px; object-fit: cover;">
                        @if($user->id === auth()->id())
                        <button class="btn btn-light btn-sm rounded-circle position-absolute bottom-0 end-0" 
                                onclick="document.getElementById('profilePictureInput').click()">
                            <i class="fas fa-camera"></i>
                        </button>
                        <input type="file" id="profilePictureInput" class="d-none" accept="image/*" onchange="uploadProfilePicture(this)">
                        @endif
                    </div>
                    
                    <!-- User Info -->
                    <div class="flex-grow-1 text-white mb-3">
                        <h2 class="mb-1">{{ $user->name }}</h2>
                        <p class="mb-2 opacity-75">{{ $user->email }}</p>
                        <div class="d-flex gap-4">
                            <div>
                                <strong id="postsCount">0</strong>
                                <span class="opacity-75">Posts</span>
                            </div>
                            <div>
                                <strong id="friendsCount">0</strong>
                                <span class="opacity-75">Friends</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="mb-3">
                        @if($user->id === auth()->id())
                        <a href="{{ route('profile.edit') }}" class="btn btn-light">
                            <i class="fas fa-edit"></i> Edit Profile
                        </a>
                        @else
                        <button class="btn btn-light" id="friendActionBtn" onclick="handleFriendAction()">
                            <i class="fas fa-user-plus"></i> <span id="friendActionText">Add Friend</span>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - About & Friends -->
        <div class="col-lg-4">
            <!-- About -->
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h6 class="mb-0">About</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <i class="fas fa-briefcase text-primary"></i>
                            <div>
                                <div class="small text-muted">Bio</div>
                                <div>{{ $user->bio ?? 'No bio yet' }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <i class="fas fa-envelope text-primary"></i>
                            <div>
                                <div class="small text-muted">Email</div>
                                <div>{{ $user->email }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="d-flex align-items-center gap-3">
                            <i class="fas fa-calendar text-primary"></i>
                            <div>
                                <div class="small text-muted">Joined</div>
                                <div>{{ $user->created_at->format('F Y') }}</div>
                            </div>
                        </div>
                    </div>
                    
                    @if($user->id === auth()->id())
                    <button class="btn btn-outline-custom w-100 mt-3" data-bs-toggle="modal" data-bs-target="#editBioModal">
                        <i class="fas fa-edit"></i> Edit Bio
                    </button>
                    @endif
                </div>
            </div>

            <!-- Friends -->
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Friends</h6>
                    <a href="{{ route('friends.index') }}" class="text-primary small">See all</a>
                </div>
                <div class="card-body" id="friendsList">
                    <div class="row g-2" id="friendsGrid">
                        <!-- Friends will be loaded here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Posts -->
        <div class="col-lg-8">
            <!-- Create Post (own profile only) -->
            @if($user->id === auth()->id())
            <div class="card-custom mb-4">
                <div class="card-body">
                    <div class="d-flex gap-3">
                        <img src="{{ auth()->user()->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.auth()->user()->name }}" 
                             alt="Profile" class="avatar">
                        <div class="flex-grow-1">
                            <textarea class="form-control border-0 bg-light" 
                                      id="postContent" 
                                      rows="3" 
                                      placeholder="Share something..."
                                      style="resize: none;"></textarea>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <button class="btn btn-light btn-sm" onclick="document.getElementById('postImage').click()">
                            <i class="fas fa-image text-success"></i> Photo
                        </button>
                        <input type="file" id="postImage" class="d-none" accept="image/*">
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
            @endif

            <!-- Posts -->
            <div id="postsContainer">
                <!-- Posts will be loaded here -->
            </div>

            <!-- No Posts Message -->
            <div id="noPostsMessage" class="text-center py-5" style="display: none;">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No posts yet</h5>
                <p class="text-muted">{{ $user->id === auth()->id() ? 'Share your first post!' : 'This user hasn\'t posted anything yet.' }}</p>
            </div>

            <!-- Loading -->
            <div class="text-center py-4" id="loadingPosts">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Bio Modal -->
<div class="modal fade" id="editBioModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Bio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="bioInput" rows="4" placeholder="Tell us about yourself...">{{ $user->bio }}</textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary-custom" onclick="updateBio()">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const userId = {{ $user->id }};
const isOwnProfile = {{ $user->id === auth()->id() ? 'true' : 'false' }};
let selectedImage = null;

// Load user stats
async function loadStats() {
    try {
        const response = await axios.get(`/api/v1/users/${userId}/posts`, {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });
        document.getElementById('postsCount').textContent = response.data.meta.total;
    } catch (error) {
        console.error('Error loading stats:', error);
    }
    
    try {
        const response = await axios.get(`/api/v1/friends`, {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });
        document.getElementById('friendsCount').textContent = response.data.meta.total;
    } catch (error) {
        console.error('Error loading friends count:', error);
    }
}

// Load Posts
async function loadPosts() {
    try {
        const response = await axios.get(`/api/v1/users/${userId}/posts`, {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });
        
        const posts = response.data.data;
        const container = document.getElementById('postsContainer');
        container.innerHTML = '';
        
        if (posts.length === 0) {
            document.getElementById('noPostsMessage').style.display = 'block';
        } else {
            posts.forEach(post => {
                const postElement = createPostElement(post);
                container.appendChild(postElement);
            });
        }
        
        document.getElementById('loadingPosts').style.display = 'none';
    } catch (error) {
        console.error('Error loading posts:', error);
    }
}

// Load Friends
async function loadFriends() {
    try {
        const response = await axios.get(`/api/v1/friends`, {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });
        
        const friends = response.data.data.slice(0, 6);
        const grid = document.getElementById('friendsGrid');
        grid.innerHTML = '';
        
        friends.forEach(friend => {
            grid.innerHTML += `
                <div class="col-4">
                    <a href="/profile/${friend.id}" class="text-decoration-none">
                        <img src="${friend.profile_picture || 'https://ui-avatars.com/api/?name='+friend.name}" 
                             class="img-fluid rounded" 
                             style="aspect-ratio: 1; object-fit: cover;"
                             title="${friend.name}">
                    </a>
                </div>
            `;
        });
        
        if (friends.length === 0) {
            grid.innerHTML = '<div class="col-12 text-center text-muted small">No friends yet</div>';
        }
    } catch (error) {
        console.error('Error loading friends:', error);
    }
}

// Check friendship status
async function checkFriendshipStatus() {
    if (isOwnProfile) return;
    
    try {
        const response = await axios.get(`/api/v1/friends/status/${userId}`, {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
        });
        
        const status = response.data.data.status;
        const btn = document.getElementById('friendActionBtn');
        const text = document.getElementById('friendActionText');
        
        switch(status) {
            case 'friends':
                btn.className = 'btn btn-success';
                text.textContent = 'Friends';
                btn.innerHTML = '<i class="fas fa-check"></i> ' + text.textContent;
                break;
            case 'pending_sent':
                btn.className = 'btn btn-secondary';
                text.textContent = 'Request Sent';
                btn.innerHTML = '<i class="fas fa-clock"></i> ' + text.textContent;
                break;
            case 'pending_received':
                btn.className = 'btn btn-primary-custom';
                text.textContent = 'Accept Request';
                btn.innerHTML = '<i class="fas fa-user-check"></i> ' + text.textContent;
                break;
            default:
                btn.className = 'btn btn-light';
                text.textContent = 'Add Friend';
                btn.innerHTML = '<i class="fas fa-user-plus"></i> ' + text.textContent;
        }
    } catch (error) {
        console.error('Error checking friendship status:', error);
    }
}

// Handle friend action
async function handleFriendAction() {
    const text = document.getElementById('friendActionText').textContent;
    
    try {
        if (text === 'Add Friend') {
            await axios.post('/api/v1/friends/request', 
                { friend_id: userId },
                { headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` } }
            );
            showToast('Friend request sent!', 'success');
            checkFriendshipStatus();
        } else if (text === 'Accept Request') {
            // Get friendship ID and accept
            const response = await axios.get('/api/v1/friends/requests', {
                headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
            });
            const friendship = response.data.data.find(f => f.id === userId);
            if (friendship) {
                await axios.post(`/api/v1/friends/${friendship.id}/accept`, {}, {
                    headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
                });
                showToast('Friend request accepted!', 'success');
                checkFriendshipStatus();
            }
        }
    } catch (error) {
        console.error('Error handling friend action:', error);
        showToast('Something went wrong', 'danger');
    }
}

// Update Bio
async function updateBio() {
    const bio = document.getElementById('bioInput').value;
    
    try {
        const formData = new FormData();
        formData.append('bio', bio);
        
        await axios.post('/api/v1/profile', formData, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                'X-HTTP-Method-Override': 'POST',
            }
        });
        
        location.reload();
    } catch (error) {
        console.error('Error updating bio:', error);
        showToast('Failed to update bio', 'danger');
    }
}

// Upload Profile Picture
async function uploadProfilePicture(input) {
    const file = input.files[0];
    if (!file) return;
    
    showLoading();
    
    const formData = new FormData();
    formData.append('profile_picture', file);
    
    try {
        await axios.post('/api/v1/profile', formData, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                'X-HTTP-Method-Override': 'POST',
                'Content-Type': 'multipart/form-data'
            }
        });
        
        location.reload();
    } catch (error) {
        console.error('Error uploading profile picture:', error);
        showToast('Failed to upload profile picture', 'danger');
        hideLoading();
    }
}

// Create Post (reuse from home page)
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
        await axios.post('/api/v1/posts', formData, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                'Content-Type': 'multipart/form-data'
            }
        });
        
        document.getElementById('postContent').value = '';
        removeImage();
        
        loadPosts();
        loadStats();
        
        showToast('Post created successfully!', 'success');
        hideLoading();
    } catch (error) {
        console.error('Error creating post:', error);
        showToast('Failed to create post', 'danger');
        hideLoading();
    }
}

// Image Preview
if (document.getElementById('postImage')) {
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
}

function removeImage() {
    selectedImage = null;
    if (document.getElementById('postImage')) {
        document.getElementById('postImage').value = '';
        document.getElementById('imagePreview').style.display = 'none';
    }
}

// Create Post Element (include the same function from home page)
function createPostElement(post) {
    // Same implementation as in home.blade.php
    return document.createElement('div'); // Placeholder - use actual implementation
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadPosts();
    loadFriends();
    if (!isOwnProfile) {
        checkFriendshipStatus();
    }
});
</script>
@endpush