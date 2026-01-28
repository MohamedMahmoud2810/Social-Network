<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SocialNet</title>
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #4F46E5;
            --primary-dark: #4338CA;
            --secondary-color: #EC4899;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-container {
            width: 100%;
            max-width: 1000px;
            margin: 2rem;
        }

        .auth-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .auth-left {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 4rem;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-left h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .auth-left p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .auth-features {
            margin-top: 3rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .auth-right {
            padding: 4rem;
        }

        .auth-right h2 {
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 0.5rem;
        }

        .auth-right .subtitle {
            color: #6B7280;
            margin-bottom: 2rem;
        }

        .form-floating input {
            border-radius: 10px;
            border: 2px solid #E5E7EB;
            transition: all 0.3s;
        }

        .form-floating input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-floating label {
            color: #6B7280;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
            padding: 0.8rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 2rem 0;
            color: #9CA3AF;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #E5E7EB;
        }

        .divider span {
            padding: 0 1rem;
        }

        .social-btn {
            border: 2px solid #E5E7EB;
            border-radius: 10px;
            padding: 0.7rem;
            background: white;
            transition: all 0.3s;
            font-weight: 500;
        }

        .social-btn:hover {
            border-color: var(--primary-color);
            background: rgba(79, 70, 229, 0.05);
        }

        @media (max-width: 768px) {
            .auth-left {
                padding: 2rem;
            }

            .auth-right {
                padding: 2rem;
            }

            .auth-left h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="row g-0">
                <!-- Left Side - Branding -->
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="auth-left">
                        <div>
                            <h1><i class="fas fa-users-between-lines"></i> SocialNet</h1>
                            <p>Connect with friends and the world around you</p>
                        </div>
                        
                        <div class="auth-features">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-user-friends"></i>
                                </div>
                                <div>
                                    <strong>Connect</strong>
                                    <div class="small opacity-75">Make friends worldwide</div>
                                </div>
                            </div>
                            
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-share-alt"></i>
                                </div>
                                <div>
                                    <strong>Share</strong>
                                    <div class="small opacity-75">Share your moments</div>
                                </div>
                            </div>
                            
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <div>
                                    <strong>Engage</strong>
                                    <div class="small opacity-75">Join conversations</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Login Form -->
                <div class="col-lg-7">
                    <div class="auth-right">
                        <h2>Welcome Back!</h2>
                        <p class="subtitle">Login to your account to continue</p>

                        @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ $errors->first() }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        <form action="{{ route('login') }}" method="POST" id="loginForm">
                            @csrf
                            
                            <div class="form-floating mb-3">
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       placeholder="name@example.com"
                                       value="{{ old('email') }}"
                                       required>
                                <label for="email">Email address</label>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Password"
                                       required>
                                <label for="password">Password</label>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                    <label class="form-check-label" for="remember">
                                        Remember me
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary-custom w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i> Login
                            </button>
                        </form>

                        <div class="divider">
                            <span>or continue with</span>
                        </div>

                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <button class="social-btn w-100">
                                    <i class="fab fa-google text-danger me-2"></i> Google
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="social-btn w-100">
                                    <i class="fab fa-facebook text-primary me-2"></i> Facebook
                                </button>
                            </div>
                        </div>

                        <div class="text-center">
                            <span class="text-muted">Don't have an account?</span>
                            <a href="{{ route('register') }}" class="fw-semibold text-decoration-none ms-1">
                                Sign up
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>