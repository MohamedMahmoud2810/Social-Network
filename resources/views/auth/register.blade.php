<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SocialNet</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root { --primary-color: #4F46E5; --primary-dark: #4338CA; --secondary-color: #EC4899; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .auth-container { width: 100%; max-width: 1000px; margin: 2rem; }
        .auth-card { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); }
        .auth-left { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); padding: 4rem; color: white; height: 100%; display: flex; flex-direction: column; justify-content: center; }
        .auth-left h1 { font-size: 3rem; font-weight: 800; margin-bottom: 1rem; }
        .auth-features { margin-top: 3rem; }
        .feature-item { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
        .feature-icon { width: 50px; height: 50px; background: rgba(255, 255, 255, 0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .auth-right { padding: 4rem; }
        .auth-right h2 { font-weight: 700; color: #1F2937; }
        .subtitle { color: #6B7280; margin-bottom: 2rem; }
        .form-floating input { border-radius: 10px; border: 2px solid #E5E7EB; }
        .form-floating input:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        .btn-primary-custom { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border: none; padding: 0.8rem; border-radius: 10px; font-weight: 600; transition: all 0.3s; color: white; }
        .btn-primary-custom:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3); color: white; }
        @media (max-width: 768px) { .auth-left { padding: 2rem; } .auth-right { padding: 2rem; } }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="row g-0">
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="auth-left">
                        <h1><i class="fas fa-users-between-lines"></i> SocialNet</h1>
                        <p>Join our community and start sharing your moments.</p>
                        <div class="auth-features">
                            <div class="feature-item">
                                <div class="feature-icon"><i class="fas fa-user-plus"></i></div>
                                <div><strong>Free Account</strong><div class="small opacity-75">Join in seconds</div></div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                                <div><strong>Secure</strong><div class="small opacity-75">Privacy focused</div></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="auth-right">
                        <h2>Create Account</h2>
                        <p class="subtitle">Get started with your free account today</p>

                        <form action="{{ route('register') }}" method="POST">
                            @csrf
                            
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" placeholder="John Doe" value="{{ old('name') }}" required>
                                <label for="name">Full Name</label>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" placeholder="name@example.com" value="{{ old('email') }}" required>
                                <label for="email">Email address</label>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" placeholder="Password" required>
                                <label for="password">Password</label>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-floating mb-4">
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>
                                <label for="password_confirmation">Confirm Password</label>
                            </div>

                            <button type="submit" class="btn btn-primary-custom w-100 mb-4">
                                <i class="fas fa-user-check me-2"></i> Create Account
                            </button>
                        </form>

                        <div class="text-center">
                            <span class="text-muted">Already have an account?</span>
                            <a href="{{ route('login') }}" class="fw-semibold text-decoration-none ms-1">Login here</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>