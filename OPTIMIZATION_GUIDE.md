# 🚀 Panduan Optimasi e-Suka

## Optimasi yang Telah Diterapkan

### 1. **Optimasi Konfigurasi**
- ✅ File konfigurasi optimasi baru: config/optimize.php
- ✅ AppServiceProvider ditingkatkan dengan:
  - Schema default string length (MySQL compatibility)
  - Force HTTPS di production
  - Rate limiting untuk API dan global requests
  - Environment-based optimizations

### 2. **Optimasi Server (.htaccess)**
- ✅ Kompresi GZIP/Deflate untuk semua aset
- ✅ Browser caching dengan expires headers
- ✅ Security headers (X-Frame-Options, X-XSS-Protection, dll)
- ✅ Cache-Control headers untuk static assets
- ✅ Optimasi untuk gambar, CSS, JavaScript, dan font

### 3. **Optimasi Build (Vite)**
- ✅ Minifikasi dengan Terser
- ✅ Remove console.log di production
- ✅ Code splitting untuk vendor chunks
- ✅ Optimasi dependencies
- ✅ Source maps dinonaktifkan di production

### 4. **Command Artisan Baru**

#### a. Optimasi Aplikasi
\\\ash
# Optimasi penuh aplikasi
php artisan app:optimize

# Clear semua cache
php artisan app:optimize --clear
\\\

#### b. Optimasi Database
\\\ash
# Optimasi tabel database
php artisan db:optimize
\\\

### 5. **Middleware Optimasi**
- ✅ OptimizeResponse middleware untuk:
  - Menambahkan security headers
  - Menghapus unnecessary headers
  - HTML minification di production

---

## 📋 Checklist Optimasi Manual

### A. **Konfigurasi PHP (php.ini)**

\\\ini
# OPcache Settings (Sangat Penting!)
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
opcache.enable_cli=1

# Memory & Upload
memory_limit=512M
upload_max_filesize=20M
post_max_size=25M
max_execution_time=300

# Session
session.gc_maxlifetime=7200
session.cookie_httponly=1
session.cookie_secure=1
\\\

### B. **Environment Variables (.env)**

\\\nv
# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://esuka.pn-lubukpakam.go.id

# Cache & Session (Gunakan Redis jika tersedia)
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=esuka_db
DB_USERNAME=esuka_user
DB_PASSWORD=strong_password_here

# Redis (Recommended untuk production)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail (Gunakan SMTP real di production)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls

# Optimization Settings
VIEW_CACHE_ENABLED=true
ROUTE_CACHE_ENABLED=true
CONFIG_CACHE_ENABLED=true
EVENT_CACHE_ENABLED=true

# Image Optimization
IMAGE_QUALITY=80
IMAGE_MAX_WIDTH=1920
IMAGE_MAX_HEIGHT=1080
\\\

### C. **Composer Optimization**

\\\ash
# Install dependencies tanpa dev packages
composer install --no-dev --optimize-autoloader

# Update autoloader
composer dump-autoload -o
\\\

### D. **NPM Build Production**

\\\ash
# Install dependencies
npm install

# Build untuk production
npm run build
\\\

### E. **Laravel Caching**

\\\ash
# Jalankan optimasi lengkap
php artisan app:optimize

# Atau manual step by step:
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
\\\

### F. **Database Optimization**

\\\ash
# Optimasi database tables
php artisan db:optimize

# Tambahkan indexes yang diperlukan (review migration files)
# Contoh menambah index:
php artisan make:migration add_indexes_to_tables
\\\

### G. **Queue Workers**

\\\ash
# Jalankan queue worker untuk background jobs
php artisan queue:work --tries=3 --timeout=90

# Untuk production, gunakan supervisor
# File config supervisor di: /etc/supervisor/conf.d/esuka-worker.conf
\\\

---

## 🔥 Optimasi Level Lanjut

### 1. **Redis Cache**

Install Redis dan update .env:
\\\ash
# Ubuntu/Debian
sudo apt install redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server

# Install PHP Redis extension
sudo apt install php-redis
\\\

### 2. **MySQL Optimization**

Tambahkan di my.cnf atau my.ini:
\\\ini
[mysqld]
# Query Cache
query_cache_type=1
query_cache_size=64M
query_cache_limit=2M

# InnoDB Settings
innodb_buffer_pool_size=1G
innodb_log_file_size=256M
innodb_flush_log_at_trx_commit=2
innodb_flush_method=O_DIRECT

# Connection Settings
max_connections=200
thread_cache_size=16
table_open_cache=2000
\\\

### 3. **CDN untuk Static Assets**

Pertimbangkan menggunakan CDN untuk:
- Images
- CSS/JS files
- Fonts
- Icons

### 4. **Database Indexes**

Review dan tambahkan indexes pada kolom yang sering di-query:
\\\sql
-- Contoh: Index pada foreign keys
CREATE INDEX idx_user_id ON pendaftaran_surat_kuasa(user_id);
CREATE INDEX idx_status ON pendaftaran_surat_kuasa(status);
CREATE INDEX idx_created_at ON pendaftaran_surat_kuasa(created_at);
\\\

### 5. **Image Optimization**

\\\ash
# Install image optimization tools
sudo apt install optipng jpegoptim

# Optimasi semua gambar di public
find public/ -name "*.png" -exec optipng {} \;
find public/ -name "*.jpg" -exec jpegoptim --max=85 {} \;
\\\

---

## 📊 Monitoring Performance

### 1. **Laravel Debugbar** (Development only)
Sudah terinstall untuk monitoring queries dan performance.

### 2. **Laravel Telescope** (Optional)
\\\ash
composer require laravel/telescope
php artisan telescope:install
php artisan migrate
\\\

### 3. **New Relic / Datadog** (Production)
Pertimbangkan untuk monitoring production yang lebih advanced.

---

## 🎯 Performance Benchmarks

### Before Optimization (Expected)
- Page load: ~3-5 seconds
- Time to First Byte (TTFB): ~800ms-1.5s
- Database queries: 50-100+ per page

### After Optimization (Target)
- Page load: ~1-2 seconds
- Time to First Byte (TTFB): ~200-400ms
- Database queries: <20 per page (dengan eager loading)

---

## ✅ Deployment Checklist

Sebelum deploy ke production:

- [ ] Update .env dengan konfigurasi production
- [ ] APP_DEBUG=false
- [ ] APP_ENV=production
- [ ] Install dependencies: composer install --no-dev --optimize-autoloader
- [ ] Build assets: 
pm run build
- [ ] Cache everything: php artisan app:optimize
- [ ] Migrate database: php artisan migrate --force
- [ ] Optimasi database: php artisan db:optimize
- [ ] Test aplikasi secara menyeluruh
- [ ] Setup queue worker dengan supervisor
- [ ] Setup backup otomatis
- [ ] Monitor logs dan errors

---

## 🆘 Troubleshooting

### Jika aplikasi lambat setelah optimasi:

1. **Clear cache dan rebuild**
   \\\ash
   php artisan app:optimize --clear
   php artisan app:optimize
   \\\

2. **Check database connections**
   \\\ash
   php artisan tinker
   DB::connection()->getPdo();
   \\\

3. **Enable query logging**
   Di AppServiceProvider, uncomment:
   \\\php
   DB::enableQueryLog();
   \\\

4. **Check server resources**
   \\\ash
   # CPU & Memory
   htop
   
   # Disk space
   df -h
   
   # MySQL processes
   mysqladmin processlist
   \\\

---

## 📚 Referensi

- [Laravel Performance](https://laravel.com/docs/11.x/deployment#optimization)
- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)
- [PHP OPcache](https://www.php.net/manual/en/book.opcache.php)
- [MySQL Performance Tuning](https://dev.mysql.com/doc/refman/8.0/en/optimization.html)

---

**Catatan**: Optimasi adalah proses berkelanjutan. Selalu monitor performance dan lakukan adjustment sesuai kebutuhan.
