#!/bin/bash

# e-Suka Deployment & Optimization Script
# Author: System Optimizer
# Description: Automate deployment and optimization process

set -e

echo "🚀 e-Suka Deployment & Optimization Script"
echo "=========================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_info() {
    echo -e "${YELLOW}➜ $1${NC}"
}

# Check if .env exists
if [ ! -f .env ]; then
    print_error ".env file not found!"
    print_info "Copying .env.example to .env..."
    cp .env.example .env
    print_success ".env created"
    print_warning "Please configure your .env file before continuing"
    exit 1
fi

# Maintenance mode
print_info "Enabling maintenance mode..."
php artisan down || true
print_success "Maintenance mode enabled"

# Git pull (if in git repository)
if [ -d .git ]; then
    print_info "Pulling latest changes from git..."
    git pull origin main || git pull origin master || print_warning "Git pull failed or not configured"
fi

# Install/Update Composer dependencies
print_info "Installing composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
print_success "Composer dependencies installed"

# Install/Update NPM dependencies
if [ -f package.json ]; then
    print_info "Installing npm dependencies..."
    npm ci --silent || npm install --silent
    print_success "NPM dependencies installed"
fi

# Build assets
print_info "Building frontend assets..."
npm run build
print_success "Assets built"

# Clear old caches
print_info "Clearing old caches..."
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
php artisan event:clear
print_success "Old caches cleared"

# Run migrations
print_info "Running database migrations..."
php artisan migrate --force
print_success "Migrations completed"

# Optimize application
print_info "Optimizing application..."
php artisan app:optimize
print_success "Application optimized"

# Optimize database
print_info "Optimizing database..."
php artisan db:optimize
print_success "Database optimized"

# Storage link
print_info "Creating storage symlink..."
php artisan storage:link || true
print_success "Storage linked"

# Set permissions
print_info "Setting permissions..."
chmod -R 755 storage bootstrap/cache
print_success "Permissions set"

# Restart queue workers (if supervisor is installed)
if command -v supervisorctl &> /dev/null; then
    print_info "Restarting queue workers..."
    supervisorctl restart esuka-worker:* || print_warning "Queue workers not configured"
fi

# Disable maintenance mode
print_info "Disabling maintenance mode..."
php artisan up
print_success "Maintenance mode disabled"

echo ""
echo "=========================================="
echo -e "${GREEN}✨ Deployment completed successfully!${NC}"
echo "=========================================="
echo ""

# Show tips
print_info "Post-deployment checklist:"
echo "  ✓ Test the application thoroughly"
echo "  ✓ Check error logs: storage/logs/laravel.log"
echo "  ✓ Monitor queue workers"
echo "  ✓ Verify database connections"
echo ""

# Show application info
print_info "Application Information:"
echo "  Environment: $(php artisan env)"
echo "  Laravel Version: $(php artisan --version)"
echo "  PHP Version: $(php -v | head -n 1)"
echo ""

print_success "Done! 🎉"
