<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class OptimizeDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize database tables for better performance';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🗄️ Optimizing database...');
        $this->newLine();

        try {
            // Get all tables
            $tables = DB::select('SHOW TABLES');
            $databaseName = DB::getDatabaseName();
            $tableColumn = "Tables_in_{$databaseName}";

            if (empty($tables)) {
                $this->warn('No tables found in database.');
                return Command::FAILURE;
            }

            $this->comment('Found ' . count($tables) . ' tables to optimize');
            $this->newLine();

            $bar = $this->output->createProgressBar(count($tables));
            $bar->start();

            foreach ($tables as $table) {
                $tableName = $table->$tableColumn;
                
                try {
                    // Optimize table
                    DB::statement("OPTIMIZE TABLE {$tableName}");
                    $bar->advance();
                } catch (\Exception $e) {
                    $this->newLine();
                    $this->warn("Could not optimize table: {$tableName}");
                }
            }

            $bar->finish();
            $this->newLine(2);

            $this->info('✨ Database optimization completed!');
            $this->newLine();

            // Show additional tips
            $this->comment('💡 Additional tips:');
            $this->line('  - Add indexes to frequently queried columns');
            $this->line('  - Use eager loading to prevent N+1 queries');
            $this->line('  - Consider using database query caching');
            $this->line('  - Review slow query log regularly');

        } catch (\Exception $e) {
            $this->error('Error optimizing database: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
