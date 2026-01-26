<?php

namespace App\Services\Domain;

use App\Models\Neighborhood;
use App\Models\SyncLog;
use Illuminate\Support\Facades\DB;
use App\Services\Domain\Contracts\ChunkAwareSync;

class NeighborhoodSyncService implements ChunkAwareSync
{
    public function sync(int $taxYear, SyncLog $log): void
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $chunkSize    = 500;
        $lastCode     = $log->last_key;       // NULL on first run
        $currentChunk = (int) $log->current_chunk;

        // 1️⃣ Count total rows once
        $totalRows = DB::table('raw_desc_r_26_neighborhood_num_adjust')->count();

        if ($totalRows === 0) {
            $log->update([
                'chunk_progress' => 100,
                'message'        => 'No neighborhoods found',
            ]);
            return;
        }

        $totalChunks = (int) ceil($totalRows / $chunkSize);

        $log->update([
            'total_chunks'   => $totalChunks,
            'current_chunk'  => $currentChunk,
            'chunk_progress' => 0,
            'message'        => 'Syncing neighborhoods',
        ]);

        while (true) {

            // 2️⃣ Fetch next deterministic range (STRICT header case)
            $rows = DB::table('raw_desc_r_26_neighborhood_num_adjust')
                ->when($lastCode, fn ($q) => $q->where('Neighborhood', '>', $lastCode))
                ->orderBy('Neighborhood')
                ->limit($chunkSize)
                ->get(['Neighborhood']);

            if ($rows->isEmpty()) {
                break;
            }

            $maxCodeInBatch = $rows->last()->Neighborhood;

            // 3️⃣ Bulk upsert via RAW SQL
            DB::statement("
                INSERT INTO neighborhoods (
                    code,
                    description,
                    adjustment_percent,
                    created_at,
                    updated_at
                )
                SELECT
                    TRIM(Neighborhood),
                    TRIM(Description),
                    adj_pcent,
                    NOW(),
                    NOW()
                FROM raw_desc_r_26_neighborhood_num_adjust
                WHERE Neighborhood > ?
                  AND Neighborhood <= ?
                ON DUPLICATE KEY UPDATE
                    description = VALUES(description),
                    adjustment_percent = VALUES(adjustment_percent),
                    updated_at = VALUES(updated_at)
            ", [$lastCode ?? '', $maxCodeInBatch]);

            // 4️⃣ Advance cursor
            $lastCode = $maxCodeInBatch;
            $currentChunk++;

            $log->update([
                'current_chunk'  => $currentChunk,
                'chunk_progress' => (int) round(($currentChunk / $totalChunks) * 100),
                'last_key'       => $lastCode,
                'message'        => "Neighborhoods synced up to {$lastCode}",
            ]);

            // 5️⃣ Connection hygiene (same as PropertySyncService)
            if ($currentChunk % 25 === 0) {
                DB::disconnect();
                DB::reconnect();
            }
        }

        $log->update([
            'chunk_progress' => 100,
            'message'        => 'Neighborhood synchronization completed',
        ]);
    }
}

