@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">Edit Profile</h5>
                </div>
                <div class="card-body">
                    <form id="editProfileForm">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control" id="bio" name="bio" rows="4" placeholder="Tell us about yourself...">{{ $user->bio }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="profile_picture" class="form-label">Profile Picture</label>
                            <div class="mb-3">
                                <img id="profilePreview" src="{{ $user->profile_picture_url ?? 'https://ui-avatars.com/api/?name='.$user->name }}" 
                                     alt="Profile" class="avatar avatar-lg mb-3">
                            </div>
                            <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                            <small class="text-muted">Recommended: Square image, at least 200x200px</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="{{ route('profile.show', auth()->id()) }}" class="btn btn-outline-custom">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Preview image on select
document.getElementById('profile_picture').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('profilePreview').src = event.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Handle form submission
document.getElementById('editProfileForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    showLoading();
    
    const formData = new FormData(this);
    
    // Ensure CSRF token is in FormData
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    formData.append('_token', csrfToken);
    
    // Log form data being sent
    console.log('Form data being sent:');
    for (let [key, value] of formData.entries()) {
        if (value instanceof File) {
            console.log(key, 'File:', value.name);
        } else {
            console.log(key, value);
        }
    }
    
    try {
        const response = await axios.post('/api/v1/profile', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        console.log('Profile update response:', response.data);
        
        showToast('Profile updated successfully!', 'success');
        hideLoading();
        
        // Redirect to profile page after 2 seconds
        setTimeout(() => {
            window.location.href = `/profile/{{ auth()->id() }}`;
        }, 2000);
    } catch (error) {
        console.error('Error updating profile:', error);
        console.error('Error response:', error.response?.data);
        const message = error.response?.data?.message || error.message || 'Failed to update profile';
        showToast(message, 'danger');
        hideLoading();
    }
});
</script>
@endpush
