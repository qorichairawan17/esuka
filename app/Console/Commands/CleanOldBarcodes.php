<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CleanOldBarcodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'barcode:clean 
                            {--days=3 : Jumlah hari kebelakang untuk menyimpan file (default: 3)}
                            {--dry-run : Jalankan tanpa menghapus file (hanya tampilkan file yang akan dihapus)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus file barcode yang lebih dari N hari yang lalu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');
        $barcodePath = storage_path('app/private/barcode');

        if (!File::isDirectory($barcodePath)) {
            $this->error("Direktori barcode tidak ditemukan: {$barcodePath}");
            return 1;
        }

        $cutoffDate = Carbon::now()->subDays($days)->startOfDay();

        $this->info("🗑️  Membersihkan file barcode yang lebih dari {$days} hari...");
        $this->info("📅 Batas tanggal: {$cutoffDate->format('Y-m-d H:i:s')}");

        if ($dryRun) {
            $this->warn("⚠️  Mode DRY-RUN: Tidak ada file yang akan dihapus.");
        }

        $this->newLine();

        $deletedCount = 0;
        $deletedSize = 0;
        $errorCount = 0;

        // Rekursif mencari semua file dalam direktori barcode
        $files = File::allFiles($barcodePath);

        if (count($files) === 0) {
            $this->info("Tidak ada file ditemukan dalam direktori barcode.");
            return 0;
        }

        $this->info("📂 Total file ditemukan: " . count($files));
        $this->newLine();

        $filesToDelete = [];

        foreach ($files as $file) {
            $lastModified = Carbon::createFromTimestamp($file->getMTime());

            // Jika file lebih tua dari batas tanggal
            if ($lastModified->lt($cutoffDate)) {
                $filesToDelete[] = [
                    'path' => $file->getPathname(),
                    'relative' => str_replace($barcodePath . DIRECTORY_SEPARATOR, '', $file->getPathname()),
                    'size' => $file->getSize(),
                    'modified' => $lastModified->format('Y-m-d H:i:s'),
                ];
            }
        }

        if (count($filesToDelete) === 0) {
            $this->info("✅ Tidak ada file yang perlu dihapus. Semua file dalam rentang {$days} hari terakhir.");
            return 0;
        }

        // Tampilkan tabel file yang akan dihapus
        $this->info("📋 File yang akan dihapus:");
        $this->table(
            ['No', 'File', 'Ukuran', 'Terakhir Diubah'],
            collect($filesToDelete)->map(function ($file, $index) {
                return [
                    $index + 1,
                    $file['relative'],
                    $this->formatBytes($file['size']),
                    $file['modified'],
                ];
            })->toArray()
        );

        $this->newLine();

        if (!$dryRun) {
            if (!$this->confirm("Apakah Anda yakin ingin menghapus " . count($filesToDelete) . " file?", true)) {
                $this->info("Operasi dibatalkan.");
                return 0;
            }

            $progressBar = $this->output->createProgressBar(count($filesToDelete));
            $progressBar->start();

            foreach ($filesToDelete as $file) {
                try {
                    if (File::delete($file['path'])) {
                        $deletedCount++;
                        $deletedSize += $file['size'];
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $this->newLine();
                    $this->error("Gagal menghapus: {$file['relative']} - {$e->getMessage()}");
                }
                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            // Hapus folder kosong setelah menghapus file
            $this->cleanEmptyDirectories($barcodePath);
        } else {
            $deletedCount = count($filesToDelete);
            $deletedSize = collect($filesToDelete)->sum('size');
        }

        // Tampilkan ringkasan
        $this->newLine();
        $this->info("═══════════════════════════════════════");
        $this->info("📊 RINGKASAN");
        $this->info("═══════════════════════════════════════");
        $this->info("   File " . ($dryRun ? "yang akan dihapus" : "dihapus") . ": {$deletedCount}");
        $this->info("   Ukuran " . ($dryRun ? "yang akan dibebaskan" : "dibebaskan") . ": " . $this->formatBytes($deletedSize));

        if ($errorCount > 0) {
            $this->error("   Error: {$errorCount}");
        }

        $this->info("═══════════════════════════════════════");

        return 0;
    }

    /**
     * Hapus direktori kosong secara rekursif
     */
    private function cleanEmptyDirectories(string $path): void
    {
        $directories = File::directories($path);

        foreach ($directories as $directory) {
            $this->cleanEmptyDirectories($directory);

            // Cek apakah direktori kosong setelah pembersihan rekursif
            if (count(File::allFiles($directory)) === 0 && count(File::directories($directory)) === 0) {
                File::deleteDirectory($directory);
                $this->line("   🗂️  Direktori kosong dihapus: " . basename($directory));
            }
        }
    }

    /**
     * Format bytes ke format yang mudah dibaca
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
