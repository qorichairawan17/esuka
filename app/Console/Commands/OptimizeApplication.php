<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class OptimizeApplication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:optimize {--clear : Clear all caches instead of optimizing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize the application for better performance';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('clear')) {
            return $this->clearOptimizations();
        }

        $this->info('🚀 Optimizing application...');
        $this->newLine();

        // Clear old caches first
        $this->comment('Clearing old caches...');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('event:clear');
        $this->info('✓ Old caches cleared');
        $this->newLine();

        // Cache configurations
        $this->comment('Caching configurations...');
        Artisan::call('config:cache');
        $this->info('✓ Configuration cached');

        // Cache routes
        $this->comment('Caching routes...');
        Artisan::call('route:cache');
        $this->info('✓ Routes cached');

        // Cache views
        $this->comment('Caching views...');
        Artisan::call('view:cache');
        $this->info('✓ Views cached');

        // Cache events
        $this->comment('Caching events...');
        Artisan::call('event:cache');
        $this->info('✓ Events cached');

        // Optimize autoloader
        $this->comment('Optimizing autoloader...');
        exec('composer dump-autoload -o 2>&1', $output, $returnCode);
        if ($returnCode === 0) {
            $this->info('✓ Autoloader optimized');
        } else {
            $this->warn('⚠ Could not optimize autoloader');
        }

        $this->newLine();
        $this->info('✨ Application optimized successfully!');
        $this->newLine();

        // Show tips
        $this->comment('💡 Additional tips:');
        $this->line('  - Use queue workers for background jobs: php artisan queue:work');
        $this->line('  - Enable OPcache in php.ini for better performance');
        $this->line('  - Use Redis for cache and sessions in production');
        $this->line('  - Build assets for production: npm run build');

        return Command::SUCCESS;
    }

    /**
     * Clear all optimizations
     */
    protected function clearOptimizations(): int
    {
        $this->info('🧹 Clearing optimizations...');
        $this->newLine();

        Artisan::call('cache:clear');
        $this->info('✓ Cache cleared');

        Artisan::call('view:clear');
        $this->info('✓ Views cleared');

        Artisan::call('config:clear');
        $this->info('✓ Configuration cleared');

        Artisan::call('route:clear');
        $this->info('✓ Routes cleared');

        Artisan::call('event:clear');
        $this->info('✓ Events cleared');

        $this->newLine();
        $this->info('✨ All optimizations cleared!');

        return Command::SUCCESS;
    }
}
