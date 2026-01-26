<?php

namespace App\Services\Domain\Contracts;

use App\Models\SyncLog;

interface ChunkAwareSync
{
    public function sync(int $taxYear, SyncLog $log): void;
}
