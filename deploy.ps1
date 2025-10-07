# e-Suka Deployment & Optimization Script (Windows)
# Author: System Optimizer
# Description: Automate deployment and optimization process for Windows

Write-Host "🚀 e-Suka Deployment & Optimization Script" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Function to print colored output
function Write-Success {
    param([string]$message)
    Write-Host "✓ $message" -ForegroundColor Green
}

function Write-Error-Custom {
    param([string]$message)
    Write-Host "✗ $message" -ForegroundColor Red
}

function Write-Warning-Custom {
    param([string]$message)
    Write-Host "⚠ $message" -ForegroundColor Yellow
}

function Write-Info {
    param([string]$message)
    Write-Host "➜ $message" -ForegroundColor Yellow
}

# Check if .env exists
if (-not (Test-Path .env)) {
    Write-Error-Custom ".env file not found!"
    Write-Info "Copying .env.example to .env..."
    Copy-Item .env.example .env
    Write-Success ".env created"
    Write-Warning-Custom "Please configure your .env file before continuing"
    exit 1
}

# Maintenance mode
Write-Info "Enabling maintenance mode..."
php artisan down 2>$null
Write-Success "Maintenance mode enabled"

# Git pull (if in git repository)
if (Test-Path .git) {
    Write-Info "Pulling latest changes from git..."
    try {
        git pull origin main 2>$null
    } catch {
        try {
            git pull origin master 2>$null
        } catch {
            Write-Warning-Custom "Git pull failed or not configured"
        }
    }
}

# Install/Update Composer dependencies
Write-Info "Installing composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
Write-Success "Composer dependencies installed"

# Install/Update NPM dependencies
if (Test-Path package.json) {
    Write-Info "Installing npm dependencies..."
    try {
        npm ci --silent
    } catch {
        npm install --silent
    }
    Write-Success "NPM dependencies installed"
}

# Build assets
Write-Info "Building frontend assets..."
npm run build
Write-Success "Assets built"

# Clear old caches
Write-Info "Clearing old caches..."
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
php artisan event:clear
Write-Success "Old caches cleared"

# Run migrations
Write-Info "Running database migrations..."
php artisan migrate --force
Write-Success "Migrations completed"

# Optimize application
Write-Info "Optimizing application..."
php artisan app:optimize
Write-Success "Application optimized"

# Optimize database
Write-Info "Optimizing database..."
php artisan db:optimize
Write-Success "Database optimized"

# Storage link
Write-Info "Creating storage symlink..."
php artisan storage:link 2>$null
Write-Success "Storage linked"

# Disable maintenance mode
Write-Info "Disabling maintenance mode..."
php artisan up
Write-Success "Maintenance mode disabled"

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "✨ Deployment completed successfully!" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Show tips
Write-Info "Post-deployment checklist:"
Write-Host "  ✓ Test the application thoroughly"
Write-Host "  ✓ Check error logs: storage/logs/laravel.log"
Write-Host "  ✓ Monitor queue workers"
Write-Host "  ✓ Verify database connections"
Write-Host ""

# Show application info
Write-Info "Application Information:"
$env = php artisan env
$version = php artisan --version
$phpVersion = php -v | Select-Object -First 1
Write-Host "  Environment: $env"
Write-Host "  Laravel Version: $version"
Write-Host "  PHP Version: $phpVersion"
Write-Host ""

Write-Success "Done! 🎉"
