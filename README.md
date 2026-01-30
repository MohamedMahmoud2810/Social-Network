# Social Networking Platform - Laravel 10

A feature-rich social networking platform built with Laravel 10, following clean architecture principles, SOLID design patterns, and modern best practices.

## 🚀 Features

### Core Features
- ✅ User Authentication (Register, Login, Logout, Password Reset)
- ✅ User Profiles (View, Edit, Profile Picture Upload)
- ✅ Friend Requests System (Send, Accept, Reject)
- ✅ Posts (Create, Read, Update, Delete with Image Upload)
- ✅ Comments on Posts
- ✅ Like/Unlike Posts
- ✅ News Feed (Posts from friends)
- ✅ User Search
- ✅ Friend Suggestions
- ✅ RESTful API with Laravel Sanctum
- ✅ Real-time Notifications (Pusher/Laravel Echo)
- ✅ Comprehensive API Documentation

### Technical Features
- Clean Architecture (Domain, Application, Infrastructure, Presentation layers)
- Repository Pattern for data access
- Service Layer for business logic
- DTOs for data transfer
- API Resources for consistent responses
- Event-driven architecture
- Comprehensive error handling
- Database migrations and seeders
- Factory pattern for testing

## 📋 Requirements

- PHP 8.1 or higher
- Composer
- MySQL 8.0 or higher
- Node.js & NPM (for frontend assets)
- Redis (optional, for caching and queues)

## 🛠️ Installation

### 1. Clone the Repository
```bash
git clone https://github.com/MohamedMahmoud2810/Social-Network
cd social-network
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=social_network
DB_USERNAME=root
DB_PASSWORD=your_password

FILESYSTEM_DISK=public

BROADCAST_DRIVER=pusher
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
```

### 4. Database Setup
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE social_network"

# Run migrations
php artisan migrate

# Seed database with sample data (optional)
php artisan db:seed
```

### 5. Storage Link
```bash
php artisan storage:link
```

### 6. Register Service Provider
Add the following to `config/app.php` in the `providers` array:
```php
App\Providers\RepositoryServiceProvider::class,
```

### 7. Compile Assets
```bash
npm run dev
# or for production
npm run build
```

### 8. Start Development Server
```bash
php artisan serve
```

Visit: `http://localhost:8000`

### 9. Queue Worker (for real-time features)
```bash
php artisan queue:work
```

## 📁 Project Structure

```
app/
├── Domain/                  # Business entities and models
│   ├── User/
│   │   └── Models/
│   ├── Post/
│   │   └── Models/
│   └── Friendship/
│       ├── Models/
│       └── Enums/
│
├── Application/            # Business logic layer
│   ├── Contracts/         # Repository interfaces
│   ├── DTOs/              # Data Transfer Objects
│   └── Services/          # Business services
│
├── Infrastructure/         # External integrations
│   ├── Repositories/      # Repository implementations
│   └── Notifications/     # Notification classes
│
└── Presentation/          # API & Web controllers
    ├── Http/
    │   ├── Controllers/
    │   │   ├── Api/V1/
    │   │   └── Web/
    │   ├── Resources/     # API Resources
    │   └── Requests/      # Form Requests
    └── Events/            # Event classes
```

## 🔑 Authentication

The API uses Laravel Sanctum for token-based authentication.

### Register
```bash
POST /api/v1/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### Login
```bash
POST /api/v1/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

Response includes `access_token` for subsequent requests.

### Making Authenticated Requests
```bash
GET /api/v1/posts
Authorization: Bearer {your-access-token}
```

## 📚 API Documentation

Full API documentation is available in [API_DOCUMENTATION.md](API_DOCUMENTATION.md)

### Quick API Reference

#### Authentication
- `POST /api/v1/register` - Register new user
- `POST /api/v1/login` - Login
- `POST /api/v1/logout` - Logout
- `GET /api/v1/me` - Get current user

#### Posts
- `GET /api/v1/posts` - Get news feed
- `POST /api/v1/posts` - Create post
- `GET /api/v1/posts/{id}` - Get single post
- `PUT /api/v1/posts/{id}` - Update post
- `DELETE /api/v1/posts/{id}` - Delete post

#### Comments
- `GET /api/v1/posts/{postId}/comments` - Get comments
- `POST /api/v1/posts/{postId}/comments` - Add comment
- `PUT /api/v1/comments/{id}` - Update comment
- `DELETE /api/v1/comments/{id}` - Delete comment

#### Likes
- `POST /api/v1/posts/{postId}/like` - Like post
- `DELETE /api/v1/posts/{postId}/unlike` - Unlike post
- `GET /api/v1/posts/{postId}/likes` - Get likes

#### Friendships
- `GET /api/v1/friends` - Get friends
- `POST /api/v1/friends/request` - Send friend request
- `POST /api/v1/friends/{id}/accept` - Accept request
- `DELETE /api/v1/friends/{userId}` - Unfriend

## 🧪 Testing

### Run Tests
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/PostTest.php

# Run with coverage
php artisan test --coverage
```

### Test User
After seeding, use these credentials:
- Email: `test@example.com`
- Password: `password`

## 🔄 Real-time Features

### Setup Laravel Echo (Frontend)
```bash
npm install --save laravel-echo pusher-js
```

### Configure Echo
```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            Authorization: 'Bearer ' + token
        }
    }
});
```

### Listen to Events
```javascript
// Listen for new posts
Echo.private(`user.${userId}`)
    .listen('.post.created', (e) => {
        console.log('New post created:', e);
    });

// Listen for friend requests
Echo.private(`user.${userId}`)
    .listen('.friend.request.sent', (e) => {
        console.log('Friend request received:', e);
    });

// Listen for post likes
Echo.private(`user.${userId}`)
    .listen('.post.liked', (e) => {
        console.log('Your post was liked:', e);
    });
```

## 🛡️ Security Features

- CSRF Protection
- SQL Injection Prevention (Eloquent ORM)
- XSS Protection
- Password Hashing (Bcrypt)
- Rate Limiting
- Input Validation
- Authentication & Authorization
- Secure File Uploads

## 📊 Database Schema

### Users Table
- id, name, email, password
- profile_picture, bio
- email_verified_at, remember_token
- timestamps, soft_deletes

### Friendships Table
- id, user_id, friend_id
- status (pending, accepted, rejected)
- timestamps

### Posts Table
- id, user_id, content
- image_path
- timestamps, soft_deletes

### Comments Table
- id, user_id, post_id
- content
- timestamps, soft_deletes

### Likes Table
- id, user_id, post_id
- timestamps

## 🎨 Design Patterns Used

1. **Repository Pattern** - Data access abstraction
2. **Service Layer Pattern** - Business logic encapsulation
3. **DTO Pattern** - Data transfer between layers
4. **Factory Pattern** - Object creation (testing)
5. **Observer Pattern** - Event handling
6. **Dependency Injection** - Loose coupling

## 🔧 Configuration

### File Upload Limits
Update `php.ini` or `.htaccess`:
```
upload_max_filesize = 10M
post_max_size = 10M
```

### Rate Limiting
Configure in `app/Http/Kernel.php`:
```php
'api' => [
    'throttle:60,1', // 60 requests per minute
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

## 📝 Development Guidelines

### Code Style
- Follow PSR-12 coding standards
- Use Laravel Pint for formatting:
  ```bash
  ./vendor/bin/pint
  ```

### Type Hints
Always use type hints for parameters and return types:
```php
public function createPost(CreatePostDTO $dto): Post
{
    // ...
}
```

### Naming Conventions
- Controllers: `{Resource}Controller`
- Models: Singular, PascalCase
- Migrations: `{action}_{table}_table`
- Services: `{Resource}Service`

## 🚀 Deployment

### Production Optimization
```bash
# Optimize configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Build production assets
npm run build
```

### Environment Variables
Set these in production:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Use production database
DB_CONNECTION=mysql
DB_HOST=your-production-host

# Use production cache/queue drivers
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

## 📖 Additional Resources

- [Laravel Documentation](https://laravel.com/docs/10.x)
- [Laravel Sanctum](https://laravel.com/docs/10.x/sanctum)
- [Laravel Echo](https://laravel.com/docs/10.x/broadcasting)
- [Pusher Documentation](https://pusher.com/docs)

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is open-sourced software licensed under the MIT license.

## 👥 Authors

- Mohamed Mahmoud - Backend Developer

## 🙏 Acknowledgments

- Laravel Framework
- Laravel Sanctum for authentication
- Pusher for real-time features
- All contributors and supporters
