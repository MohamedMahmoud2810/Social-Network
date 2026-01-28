#!/bin/bash

# Social Network Platform - Installation Script
# Laravel 10 Setup Automation

set -e

echo "========================================="
echo "Social Network Platform - Installation"
echo "========================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Check if running as root
if [ "$EUID" -eq 0 ]; then 
    echo -e "${RED}Please do not run this script as root${NC}"
    exit 1
fi

# Function to print colored output
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_info() {
    echo -e "${YELLOW}→ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

# Check PHP version
print_info "Checking PHP version..."
PHP_VERSION=$(php -r "echo PHP_VERSION;" 2>/dev/null || echo "0")
if [ "$(printf '%s\n' "8.1" "$PHP_VERSION" | sort -V | head -n1)" != "8.1" ]; then
    print_error "PHP 8.1 or higher is required. Current version: $PHP_VERSION"
    exit 1
fi
print_success "PHP version check passed ($PHP_VERSION)"

# Check if Composer is installed
print_info "Checking Composer..."
if ! command -v composer &> /dev/null; then
    print_error "Composer is not installed. Please install Composer first."
    exit 1
fi
print_success "Composer found"

# Check if Node.js is installed
print_info "Checking Node.js..."
if ! command -v node &> /dev/null; then
    print_error "Node.js is not installed. Please install Node.js first."
    exit 1
fi
print_success "Node.js found ($(node --version))"

# Install Composer dependencies
print_info "Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader
print_success "Composer dependencies installed"

# Install NPM dependencies
print_info "Installing NPM dependencies..."
npm install
print_success "NPM dependencies installed"

# Copy .env file if it doesn't exist
if [ ! -f .env ]; then
    print_info "Creating .env file..."
    cp .env.example .env
    print_success ".env file created"
else
    print_info ".env file already exists"
fi

# Generate application key
print_info "Generating application key..."
php artisan key:generate --ansi
print_success "Application key generated"

# Prompt for database credentials
echo ""
print_info "Database Configuration"
echo ""
read -p "Database name (default: social_network): " DB_NAME
DB_NAME=${DB_NAME:-social_network}

read -p "Database username (default: root): " DB_USER
DB_USER=${DB_USER:-root}

read -sp "Database password: " DB_PASS
echo ""

read -p "Database host (default: 127.0.0.1): " DB_HOST
DB_HOST=${DB_HOST:-127.0.0.1}

read -p "Database port (default: 3306): " DB_PORT
DB_PORT=${DB_PORT:-3306}

# Update .env file with database credentials
print_info "Updating .env file with database credentials..."
sed -i.bak "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i.bak "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i.bak "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env
sed -i.bak "s/DB_HOST=.*/DB_HOST=$DB_HOST/" .env
sed -i.bak "s/DB_PORT=.*/DB_PORT=$DB_PORT/" .env
rm .env.bak
print_success "Database credentials updated"

# Create database if it doesn't exist
print_info "Creating database if it doesn't exist..."
mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || {
    print_error "Failed to create database. Please create it manually."
}
print_success "Database ready"

# Run migrations
print_info "Running database migrations..."
php artisan migrate --force
print_success "Migrations completed"

# Ask if user wants to seed the database
echo ""
read -p "Do you want to seed the database with sample data? (y/N): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    print_info "Seeding database..."
    php artisan db:seed
    print_success "Database seeded successfully"
    echo ""
    echo -e "${GREEN}Test user credentials:${NC}"
    echo "Email: test@example.com"
    echo "Password: password"
fi

# Create storage link
print_info "Creating storage link..."
php artisan storage:link
print_success "Storage link created"

# Setup Pusher (optional)
echo ""
read -p "Do you want to configure Pusher for real-time features? (y/N): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo ""
    print_info "Pusher Configuration"
    read -p "Pusher App ID: " PUSHER_APP_ID
    read -p "Pusher App Key: " PUSHER_APP_KEY
    read -p "Pusher App Secret: " PUSHER_APP_SECRET
    read -p "Pusher App Cluster (default: mt1): " PUSHER_APP_CLUSTER
    PUSHER_APP_CLUSTER=${PUSHER_APP_CLUSTER:-mt1}
    
    sed -i.bak "s/PUSHER_APP_ID=.*/PUSHER_APP_ID=$PUSHER_APP_ID/" .env
    sed -i.bak "s/PUSHER_APP_KEY=.*/PUSHER_APP_KEY=$PUSHER_APP_KEY/" .env
    sed -i.bak "s/PUSHER_APP_SECRET=.*/PUSHER_APP_SECRET=$PUSHER_APP_SECRET/" .env
    sed -i.bak "s/PUSHER_APP_CLUSTER=.*/PUSHER_APP_CLUSTER=$PUSHER_APP_CLUSTER/" .env
    sed -i.bak "s/BROADCAST_DRIVER=.*/BROADCAST_DRIVER=pusher/" .env
    rm .env.bak
    print_success "Pusher configured"
fi

# Compile frontend assets
echo ""
read -p "Do you want to compile frontend assets? (y/N): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    print_info "Compiling frontend assets..."
    npm run build
    print_success "Frontend assets compiled"
fi

# Clear and cache config
print_info "Optimizing application..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
print_success "Application optimized"

echo ""
echo "========================================="
echo -e "${GREEN}Installation completed successfully!${NC}"
echo "========================================="
echo ""
echo "To start the development server, run:"
echo -e "${YELLOW}php artisan serve${NC}"
echo ""
echo "Your application will be available at:"
echo -e "${YELLOW}http://localhost:8000${NC}"
echo ""
echo "API base URL:"
echo -e "${YELLOW}http://localhost:8000/api/v1${NC}"
echo ""
echo "To run the queue worker (for real-time features):"
echo -e "${YELLOW}php artisan queue:work${NC}"
echo ""
echo "For more information, see README.md and API_DOCUMENTATION.md"
echo ""