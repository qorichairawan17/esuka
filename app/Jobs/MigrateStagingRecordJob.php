<?php

namespace App\Jobs;

use App\Models\Sync\StagingSyncSuratKuasaModel;
use App\Services\SyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MigrateStagingRecordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public StagingSyncSuratKuasaModel $stagingRecord,
        public ?int $authId
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(SyncService $syncService): void
    {
        // Panggil method yang berisi logika migrasi dari SyncService
        $syncService->processSingleMigration($this->stagingRecord, $this->authId);
    }
}
