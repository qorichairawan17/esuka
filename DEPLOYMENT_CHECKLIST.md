# DEPLOYMENT CHECKLIST e-SUKA

## Pre-Deployment

### Environment
- [ ] Copy .env.production.example to .env
- [ ] Set APP_ENV=production
- [ ] Set APP_DEBUG=false
- [ ] Configure database
- [ ] Configure Redis
- [ ] Configure mail

### Build
- [ ] composer install --no-dev --optimize-autoloader
- [ ] npm run build
- [ ] php artisan app:optimize

### Server
- [ ] Configure web server (Apache/Nginx)
- [ ] Setup SSL certificate
- [ ] Configure queue workers
- [ ] Enable OPcache

### Testing
- [ ] Test all features
- [ ] Run php artisan app:performance-check
- [ ] Check logs

## Post-Deployment
- [ ] Monitor logs
- [ ] Check performance
- [ ] Verify backups

