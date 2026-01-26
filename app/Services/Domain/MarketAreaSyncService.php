<?php

namespace App\Services\Domain;

use App\Models\MarketArea;
use App\Models\SyncLog;
use Illuminate\Support\Facades\DB;
use App\Services\Domain\Contracts\ChunkAwareSync;

class MarketAreaSyncService implements ChunkAwareSync
{
    public function sync(int $taxYear, SyncLog $log): void
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $chunkSize    = 500;
        $lastCode     = $log->last_key;       // NULL on first run
        $currentChunk = (int) $log->current_chunk;

        // 1️⃣ Count total rows once
        $totalRows = DB::table('raw_desc_r_21_market_area')->count();

        if ($totalRows === 0) {
            $log->update([
                'chunk_progress' => 100,
                'message'        => 'No market areas found',
            ]);
            return;
        }

        $totalChunks = (int) ceil($totalRows / $chunkSize);

        $log->update([
            'total_chunks'   => $totalChunks,
            'current_chunk'  => $currentChunk,
            'chunk_progress' => 0,
            'message'        => 'Syncing market areas',
        ]);

        while (true) {

            // 2️⃣ Fetch next deterministic range
            $rows = DB::table('raw_desc_r_21_market_area')
                ->when($lastCode, fn ($q) => $q->where('MktArea', '>', $lastCode))
                ->orderBy('MktArea')
                ->limit($chunkSize)
                ->get(['MktArea']);

            if ($rows->isEmpty()) {
                break;
            }

            $maxCodeInBatch = $rows->last()->MktArea;

            // 3️⃣ Bulk upsert via raw SQL
            DB::statement("
                INSERT INTO market_areas (code, description, created_at, updated_at)
                SELECT
                    TRIM(MktArea),
                    TRIM(Description),
                    NOW(),
                    NOW()
                FROM raw_desc_r_21_market_area
                WHERE MktArea > ?
                  AND MktArea <= ?
                ON DUPLICATE KEY UPDATE
                    description = VALUES(Description),
                    updated_at  = VALUES(updated_at)
            ", [$lastCode ?? '', $maxCodeInBatch]);

            // 4️⃣ Advance cursor
            $lastCode = $maxCodeInBatch;
            $currentChunk++;

            $log->update([
                'current_chunk'  => $currentChunk,
                'chunk_progress' => (int) round(($currentChunk / $totalChunks) * 100),
                'last_key'       => $lastCode,
                'message'        => "Market areas synced up to {$lastCode}",
            ]);

            // 5️⃣ Connection hygiene
            if ($currentChunk % 25 === 0) {
                DB::disconnect();
                DB::reconnect();
            }
        }

        $log->update([
            'chunk_progress' => 100,
            'message'        => 'Market area synchronization completed',
        ]);
    }
}

