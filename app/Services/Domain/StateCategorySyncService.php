<?php

namespace App\Services\Domain;

use App\Models\StateCategory;
use App\Models\SyncLog;
use Illuminate\Support\Facades\DB;
use App\Services\Domain\Contracts\ChunkAwareSync;

class StateCategorySyncService implements ChunkAwareSync
{
    public function sync(int $taxYear, SyncLog $log): void
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $chunkSize    = 500;
        $lastCode     = $log->last_key;       // NULL on first run
        $currentChunk = (int) $log->current_chunk;

        // 1️⃣ Count once
        $totalRows = DB::table('raw_desc_r_01_state_class')->count();

        if ($totalRows === 0) {
            $log->update([
                'chunk_progress' => 100,
                'message'        => 'No state categories found',
            ]);
            return;
        }

        $totalChunks = (int) ceil($totalRows / $chunkSize);

        $log->update([
            'total_chunks'   => $totalChunks,
            'current_chunk'  => $currentChunk,
            'chunk_progress' => 0,
            'message'        => 'Syncing state categories',
        ]);

        while (true) {

            // 2️⃣ Fetch next range (STRICT header case)
            $rows = DB::table('raw_desc_r_01_state_class')
                ->when($lastCode, fn ($q) => $q->where('Code', '>', $lastCode))
                ->orderBy('Code')
                ->limit($chunkSize)
                ->get(['Code']);

            if ($rows->isEmpty()) {
                break;
            }

            $maxCodeInBatch = $rows->last()->Code;

            // 3️⃣ RAW bulk upsert
            DB::statement("
                INSERT INTO state_categories (
                    code,
                    description,
                    created_at,
                    updated_at
                )
                SELECT
                    TRIM(Code),
                    TRIM(Description),
                    NOW(),
                    NOW()
                FROM raw_desc_r_01_state_class
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
                'message'        => "State categories synced up to {$lastCode}",
            ]);

            // 5️⃣ Connection hygiene (same as PropertySyncService)
            if ($currentChunk % 25 === 0) {
                DB::disconnect();
                DB::reconnect();
            }
        }

        $log->update([
            'chunk_progress' => 100,
            'message'        => 'State category synchronization completed',
        ]);
    }
}

