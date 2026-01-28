/**
 * SocialNet - Main JavaScript Application
 * Handles all frontend interactions with the API
 */

// Configuration
const API_BASE_URL = '/api/v1';
let authToken = localStorage.getItem('auth_token');

// Axios Default Configuration
axios.defaults.baseURL = API_BASE_URL;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

if (authToken) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`;
}

// Axios Response Interceptor for Token Refresh
axios.interceptors.response.use(
    response => response,
    async error => {
        const originalRequest = error.config;
        
        if (error.response?.status === 401 && !originalRequest._retry) {
            originalRequest._retry = true;
            
            try {
                const response = await axios.post('/refresh');
                const newToken = response.data.data.access_token;
                
                localStorage.setItem('auth_token', newToken);
                axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`;
                originalRequest.headers['Authorization'] = `Bearer ${newToken}`;
                
                return axios(originalRequest);
            } catch (refreshError) {
                localStorage.removeItem('auth_token');
                window.location.href = '/login';
                return Promise.reject(refreshError);
            }
        }
        
        return Promise.reject(error);
    }
);

/**
 * Authentication Functions
 */
const Auth = {
    // Login
    async login(email, password, remember = false) {
        try {
            const response = await axios.post('/login', {
                email,
                password,
                remember
            });
            
            const token = response.data.data.access_token;
            localStorage.setItem('auth_token', token);
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            
            return response.data;
        } catch (error) {
            throw error.response?.data?.message || 'Login failed';
        }
    },
    
    // Register
    async register(name, email, password, passwordConfirmation) {
        try {
            const response = await axios.post('/register', {
                name,
                email,
                password,
                password_confirmation: passwordConfirmation
            });
            
            const token = response.data.data.access_token;
            localStorage.setItem('auth_token', token);
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            
            return response.data;
        } catch (error) {
            throw error.response?.data || 'Registration failed';
        }
    },
    
    // Logout
    async logout() {
        try {
            await axios.post('/logout');
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            localStorage.removeItem('auth_token');
            delete axios.defaults.headers.common['Authorization'];
            window.location.href = '/login';
        }
    },
    
    // Get Current User
    async getCurrentUser() {
        try {
            const response = await axios.get('/me');
            return response.data.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to get user';
        }
    }
};

/**
 * User/Profile Functions
 */
const User = {
    // Get User Profile
    async getProfile(userId) {
        try {
            const response = await axios.get(`/users/${userId}`);
            return response.data.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to get profile';
        }
    },
    
    // Update Profile
    async updateProfile(data) {
        try {
            const formData = new FormData();
            
            for (let key in data) {
                if (data[key] !== null && data[key] !== undefined) {
                    formData.append(key, data[key]);
                }
            }
            
            const response = await axios.post('/profile', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-HTTP-Method-Override': 'PUT'
                }
            });
            
            return response.data.data;
        } catch (error) {
            throw error.response?.data || 'Failed to update profile';
        }
    },
    
    // Search Users
    async search(query, perPage = 15) {
        try {
            const response = await axios.get('/users/search', {
                params: { q: query, per_page: perPage }
            });
            return response.data;
        } catch (error) {
            throw error.response?.data?.message || 'Search failed';
        }
    },
    
    // Get Suggestions
    async getSuggestions(limit = 10) {
        try {
            const response = await axios.get('/users/suggestions', {
                params: { limit }
            });
            return response.data.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to get suggestions';
        }
    }
};

/**
 * Post Functions
 */
const Post = {
    // Get News Feed
    async getNewsFeed(page = 1, perPage = 15) {
        try {
            const response = await axios.get('/posts', {
                params: { page, per_page: perPage }
            });
            return response.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to load posts';
        }
    },
    
    // Get User Posts
    async getUserPosts(userId, page = 1, perPage = 15) {
        try {
            const response = await axios.get(`/users/${userId}/posts`, {
                params: { page, per_page: perPage }
            });
            return response.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to load posts';
        }
    },
    
    // Create Post
    async create(content, image = null) {
        try {
            const formData = new FormData();
            formData.append('content', content);
            
            if (image) {
                formData.append('image', image);
            }
            
            const response = await axios.post('/posts', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            
            return response.data.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to create post';
        }
    },
    
    // Update Post
    async update(postId, content, image = null, removeImage = false) {
        try {
            const formData = new FormData();
            formData.append('content', content);
            
            if (image) {
                formData.append('image', image);
            }
            
            if (removeImage) {
                formData.append('remove_image', 'true');
            }
            
            const response = await axios.post(`/posts/${postId}`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-HTTP-Method-Override': 'PUT'
                }
            });
            
            return response.data.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to update post';
        }
    },
    
    // Delete Post
    async delete(postId) {
        try {
            const response = await axios.delete(`/posts/${postId}`);
            return response.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to delete post';
        }
    },
    
    // Like Post
    async like(postId) {
        try {
            const response = await axios.post(`/posts/${postId}/like`);
            return response.data.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to like post';
        }
    },
    
    // Unlike Post
    async unlike(postId) {
        try {
            const response = await axios.delete(`/posts/${postId}/unlike`);
            return response.data.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to unlike post';
        }
    },
    
    // Get Post Likes
    async getLikes(postId) {
        try {
            const response = await axios.get(`/posts/${postId}/likes`);
            return response.data.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to get likes';
        }
    },
    
    // Get Trending Posts
    async getTrending(perPage = 15) {
        try {
            const response = await axios.get('/posts/trending', {
                params: { per_page: perPage }
            });
            return response.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to get trending posts';
        }
    },
    
    // Search Posts
    async search(query, perPage = 15) {
        try {
            const response = await axios.get('/posts/search', {
                params: { q: query, per_page: perPage }
            });
            return response.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to search posts';
        }
    }
};

/**
 * Comment Functions
 */
const Comment = {
    // Get Comments
    async getComments(postId) {
        try {
            const response = await axios.get(`/posts/${postId}/comments`);
            return response.data.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to get comments';
        }
    },
    
    // Add Comment
    async create(postId, content) {
        try {
            const response = await axios.post(`/posts/${postId}/comments`, {
                content
            });
            return response.data.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to add comment';
        }
    },
    
    // Update Comment
    async update(commentId, content) {
        try {
            const response = await axios.put(`/comments/${commentId}`, {
                content
            });
            return response.data.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to update comment';
        }
    },
    
    // Delete Comment
    async delete(commentId) {
        try {
            const response = await axios.delete(`/comments/${commentId}`);
            return response.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to delete comment';
        }
    }
};

/**
 * Friendship Functions
 */
const Friendship = {
    // Get Friends
    async getFriends() {
        try {
            const response = await axios.get('/friends');
            return response.data.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to get friends';
        }
    },
    
    // Get Friend Requests
    async getRequests() {
        try {
            const response = await axios.get('/friends/requests');
            return response.data.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to get requests';
        }
    },
    
    // Get Pending Requests
    async getPending() {
        try {
            const response = await axios.get('/friends/pending');
            return response.data.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to get pending requests';
        }
    },
    
    // Send Friend Request
    async sendRequest(friendId) {
        try {
            const response = await axios.post('/friends/request', {
                friend_id: friendId
            });
            return response.data.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to send request';
        }
    },
    
    // Accept Friend Request
    async accept(friendshipId) {
        try {
            const response = await axios.post(`/friends/${friendshipId}/accept`);
            return response.data.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to accept request';
        }
    },
    
    // Reject Friend Request
    async reject(friendshipId) {
        try {
            const response = await axios.post(`/friends/${friendshipId}/reject`);
            return response.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to reject request';
        }
    },
    
    // Unfriend
    async unfriend(userId) {
        try {
            const response = await axios.delete(`/friends/${userId}`);
            return response.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to unfriend';
        }
    },
    
    // Get Friendship Status
    async getStatus(userId) {
        try {
            const response = await axios.get(`/friends/status/${userId}`);
            return response.data.data.status;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to get status';
        }
    },
    
    // Get Mutual Friends
    async getMutualFriends(userId) {
        try {
            const response = await axios.get(`/friends/mutual/${userId}`);
            return response.data.data;
        } catch (error) {
            throw error.response?.data?.message || 'Failed to get mutual friends';
        }
    }
};

/**
 * Utility Functions
 */
const Utils = {
    // Format Time
    formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);
        
        if (diff < 60) return 'Just now';
        if (diff < 3600) return Math.floor(diff / 60) + ' minutes ago';
        if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
        if (diff < 604800) return Math.floor(diff / 86400) + ' days ago';
        
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    },
    
    // Show Toast Notification
    showToast(message, type = 'success') {
        const toastHtml = `
            <div class="toast toast-custom align-items-center text-white bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        const container = document.querySelector('.toast-container');
        if (container) {
            container.insertAdjacentHTML('beforeend', toastHtml);
            
            const toastElement = container.lastElementChild;
            const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 3000 });
            toast.show();
            
            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        }
    },
    
    // Show Loading Spinner
    showLoading() {
        const spinner = document.createElement('div');
        spinner.className = 'spinner-overlay';
        spinner.id = 'globalSpinner';
        spinner.innerHTML = '<div class="spinner-custom"></div>';
        document.body.appendChild(spinner);
    },
    
    // Hide Loading Spinner
    hideLoading() {
        const spinner = document.getElementById('globalSpinner');
        if (spinner) spinner.remove();
    },
    
    // Validate Email
    isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },
    
    // Validate Password Strength
    checkPasswordStrength(password) {
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]+/)) strength++;
        if (password.match(/[A-Z]+/)) strength++;
        if (password.match(/[0-9]+/)) strength++;
        if (password.match(/[$@#&!]+/)) strength++;
        
        if (strength <= 2) return { level: 'weak', score: strength };
        if (strength <= 4) return { level: 'medium', score: strength };
        return { level: 'strong', score: strength };
    },
    
    // Debounce Function
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },
    
    // Truncate Text
    truncate(text, length = 100) {
        if (text.length <= length) return text;
        return text.substring(0, length) + '...';
    }
};

// Export to global scope
window.SocialNet = {
    Auth,
    User,
    Post,
    Comment,
    Friendship,
    Utils
};

// Backward compatibility
window.showToast = Utils.showToast;
window.showLoading = Utils.showLoading;
window.hideLoading = Utils.hideLoading;

console.log('SocialNet app initialized ✓');