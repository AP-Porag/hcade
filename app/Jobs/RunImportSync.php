<?php

namespace App\Jobs;

use App\Models\SyncLog;
use App\Services\Domain\DomainSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RunImportSync implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $taxYear,
        public int $logId,
        public string $syncContext
    )
    {

    }

    /**
     * Execute the job.
     */
    public function handle(DomainSyncService $service): void
    {
        $log = SyncLog::findOrFail($this->logId);

        if ($this->syncContext == 'domain'){
            $service->sync($this->taxYear, $log);
        }else{
            return;
            //run RawSyncService here
        }

    }
}
