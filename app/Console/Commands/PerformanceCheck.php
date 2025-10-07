<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PerformanceCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:performance-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check application performance and provide optimization suggestions';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Checking Application Performance...');
        $this->newLine();

        $score = 100;
        $issues = [];
        $suggestions = [];

        // 1. Check environment
        $this->comment('1. Environment Configuration');
        if (config('app.debug') === true) {
            $score -= 15;
            $issues[] = 'APP_DEBUG is enabled';
            $suggestions[] = 'Set APP_DEBUG=false in production';
            $this->warn('  ✗ DEBUG mode is enabled (-15 points)');
        } else {
            $this->info('  ✓ DEBUG mode is disabled');
        }

        if (config('app.env') !== 'production') {
            $score -= 10;
            $issues[] = 'Not running in production environment';
            $suggestions[] = 'Set APP_ENV=production';
            $this->warn('  ✗ Not in production environment (-10 points)');
        } else {
            $this->info('  ✓ Running in production environment');
        }

        $this->newLine();

        // 2. Check caching
        $this->comment('2. Caching Configuration');
        
        // Config cache
        if (file_exists(base_path('bootstrap/cache/config.php'))) {
            $this->info('  ✓ Config is cached');
        } else {
            $score -= 10;
            $issues[] = 'Config is not cached';
            $suggestions[] = 'Run: php artisan config:cache';
            $this->warn('  ✗ Config is not cached (-10 points)');
        }

        // Route cache
        if (file_exists(base_path('bootstrap/cache/routes-v7.php'))) {
            $this->info('  ✓ Routes are cached');
        } else {
            $score -= 10;
            $issues[] = 'Routes are not cached';
            $suggestions[] = 'Run: php artisan route:cache';
            $this->warn('  ✗ Routes are not cached (-10 points)');
        }

        // View cache
        $viewCacheFiles = File::files(storage_path('framework/views'));
        if (count($viewCacheFiles) > 0) {
            $this->info('  ✓ Views are compiled (' . count($viewCacheFiles) . ' files)');
        } else {
            $score -= 5;
            $issues[] = 'Views are not compiled';
            $suggestions[] = 'Run: php artisan view:cache';
            $this->warn('  ✗ Views are not compiled (-5 points)');
        }

        $this->newLine();

        // 3. Check cache driver
        $this->comment('3. Cache & Session Drivers');
        $cacheDriver = config('cache.default');
        if (in_array($cacheDriver, ['redis', 'memcached'])) {
            $this->info("  ✓ Using fast cache driver: $cacheDriver");
        } else {
            $score -= 15;
            $issues[] = "Slow cache driver: $cacheDriver";
            $suggestions[] = 'Use Redis or Memcached for better performance';
            $this->warn("  ✗ Using slow cache driver: $cacheDriver (-15 points)");
        }

        $sessionDriver = config('session.driver');
        if (in_array($sessionDriver, ['redis', 'memcached'])) {
            $this->info("  ✓ Using fast session driver: $sessionDriver");
        } else {
            $score -= 10;
            $issues[] = "Slow session driver: $sessionDriver";
            $suggestions[] = 'Use Redis or Memcached for sessions';
            $this->warn("  ✗ Using slow session driver: $sessionDriver (-10 points)");
        }

        $this->newLine();

        // 4. Check queue configuration
        $this->comment('4. Queue Configuration');
        $queueDriver = config('queue.default');
        if ($queueDriver === 'sync') {
            $score -= 10;
            $issues[] = 'Using sync queue driver';
            $suggestions[] = 'Use database, redis, or beanstalkd for queues';
            $this->warn("  ✗ Using sync queue driver (-10 points)");
        } else {
            $this->info("  ✓ Using async queue driver: $queueDriver");
        }

        $this->newLine();

        // 5. Check composer autoloader
        $this->comment('5. Composer Optimization');
        $composerLock = json_decode(file_get_contents(base_path('composer.lock')), true);
        $devPackages = count($composerLock['packages-dev'] ?? []);
        if ($devPackages > 0) {
            $score -= 5;
            $issues[] = "$devPackages dev packages are installed";
            $suggestions[] = 'Run: composer install --no-dev --optimize-autoloader';
            $this->warn("  ✗ $devPackages dev packages installed (-5 points)");
        } else {
            $this->info('  ✓ No dev packages installed');
        }

        $this->newLine();

        // 6. Check database
        $this->comment('6. Database Configuration');
        try {
            $startTime = microtime(true);
            DB::connection()->getPdo();
            $connectionTime = round((microtime(true) - $startTime) * 1000, 2);
            
            if ($connectionTime < 10) {
                $this->info("  ✓ Database connection: {$connectionTime}ms (Excellent)");
            } elseif ($connectionTime < 50) {
                $this->info("  ✓ Database connection: {$connectionTime}ms (Good)");
            } else {
                $score -= 5;
                $this->warn("  ⚠ Database connection: {$connectionTime}ms (Slow)");
                $suggestions[] = 'Consider using a local database or check network latency';
            }
        } catch (\Exception $e) {
            $score -= 20;
            $this->error('  ✗ Cannot connect to database');
            $issues[] = 'Database connection failed';
        }

        $this->newLine();

        // 7. Check OPcache
        $this->comment('7. PHP OPcache');
        if (function_exists('opcache_get_status')) {
            $opcache = opcache_get_status();
            if ($opcache && $opcache['opcache_enabled']) {
                $this->info('  ✓ OPcache is enabled');
            } else {
                $score -= 15;
                $issues[] = 'OPcache is not enabled';
                $suggestions[] = 'Enable OPcache in php.ini for better performance';
                $this->warn('  ✗ OPcache is not enabled (-15 points)');
            }
        } else {
            $score -= 15;
            $issues[] = 'OPcache is not available';
            $suggestions[] = 'Install and enable OPcache PHP extension';
            $this->warn('  ✗ OPcache is not available (-15 points)');
        }

        $this->newLine();

        // Final Score
        $score = max(0, $score);
        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        if ($score >= 90) {
            $this->info("🏆 Performance Score: $score/100 (Excellent!)");
        } elseif ($score >= 70) {
            $this->warn("⭐ Performance Score: $score/100 (Good)");
        } elseif ($score >= 50) {
            $this->warn("⚠️  Performance Score: $score/100 (Needs Improvement)");
        } else {
            $this->error("❌ Performance Score: $score/100 (Critical)");
        }
        
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        // Show issues and suggestions
        if (count($issues) > 0) {
            $this->warn('⚠️  Issues Found:');
            foreach ($issues as $issue) {
                $this->line("  • $issue");
            }
            $this->newLine();

            $this->info('💡 Suggestions:');
            foreach ($suggestions as $suggestion) {
                $this->line("  • $suggestion");
            }
            $this->newLine();
        } else {
            $this->info('✨ No issues found! Your application is well optimized.');
        }

        // Quick fix option
        if (count($suggestions) > 0) {
            if ($this->confirm('Would you like to run automatic optimizations now?', true)) {
                $this->newLine();
                $this->call('app:optimize');
            }
        }

        return Command::SUCCESS;
    }
}
