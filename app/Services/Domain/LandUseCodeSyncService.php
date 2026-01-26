<?php

namespace App\Services\Domain;

use App\Models\LandUseCode;
use App\Models\SyncLog;
use App\Models\Valuation;
use Illuminate\Support\Facades\DB;
use App\Services\Domain\Contracts\ChunkAwareSync;

class LandUseCodeSyncService implements ChunkAwareSync
{
    public function sync(int $taxYear, SyncLog $log): void
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $chunkSize    = 500;
        $lastCode     = $log->last_key;       // NULL on first run
        $currentChunk = (int) $log->current_chunk;

        // 1️⃣ Count total rows ONCE
        $totalRows = DB::table('raw_desc_r_15_land_usecode')->count();

        if ($totalRows === 0) {
            $log->update([
                'chunk_progress' => 100,
                'message'        => 'No land use codes found',
            ]);
            return;
        }

        $totalChunks = (int) ceil($totalRows / $chunkSize);

        $log->update([
            'total_chunks'   => $totalChunks,
            'current_chunk'  => $currentChunk,
            'chunk_progress' => 0,
            'message'        => 'Syncing land use codes',
        ]);

        while (true) {

            // 2️⃣ Fetch next deterministic batch
            $rows = DB::table('raw_desc_r_15_land_usecode')
                ->when($lastCode, fn ($q) => $q->where('Code', '>', $lastCode))
                ->orderBy('Code')
                ->limit($chunkSize)
                ->get(['Code']);

            if ($rows->isEmpty()) {
                break;
            }

            $maxCodeInBatch = $rows->last()->Code;

            // 3️⃣ Bulk upsert via raw SQL
            DB::statement("
                INSERT INTO land_use_codes (code, description, created_at, updated_at)
                SELECT
                    TRIM(Code),
                    TRIM(Description),
                    NOW(),
                    NOW()
                FROM raw_desc_r_15_land_usecode
                WHERE Code > ?
                  AND Code <= ?
                ON DUPLICATE KEY UPDATE
                    description = VALUES(description),
                    updated_at  = VALUES(updated_at)
            ", [$lastCode ?? '', $maxCodeInBatch]);

            // 4️⃣ Advance cursor
            $lastCode = $maxCodeInBatch;
            $currentChunk++;

            $log->update([
                'current_chunk'  => $currentChunk,
                'chunk_progress' => (int) round(($currentChunk / $totalChunks) * 100),
                'last_key'       => $lastCode,
                'message'        => "Land use codes synced up to {$lastCode}",
            ]);

            // 5️⃣ Connection hygiene
            if ($currentChunk % 25 === 0) {
                DB::disconnect();
                DB::reconnect();
            }
        }

        $log->update([
            'chunk_progress' => 100,
            'message'        => 'Land use code synchronization completed',
        ]);
    }
}

