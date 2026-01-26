<?php

namespace App\Services\Domain;

use App\Models\BuildingStyle;
use App\Models\SyncLog;
use App\Services\Domain\Contracts\ChunkAwareSync;
use Illuminate\Support\Facades\DB;

class BuildingStyleSyncService implements ChunkAwareSync
{
    public function sync(int $taxYear, SyncLog $log): void
    {
        // Prepare base query (RAW table, exact column names)
        $query = DB::table('raw_desc_r_03_building_style')
            ->orderBy('Code');

        // Chunk settings
        $chunkSize = 500;

        // Count total rows ONCE (safe for lookup table)
        $totalRows = $query->count();
        $totalChunks = (int) ceil($totalRows / $chunkSize);

        // Initialize chunk tracking in sync_logs
        $log->update([
            'total_chunks'  => $totalChunks,
            'current_chunk' => 0,
            'chunk_progress'=> 0,
            'message'       => 'Syncing building styles',
        ]);

        $currentChunk = 0;

        // Chunk processing
        $query->chunk($chunkSize, function ($rows) use (
            &$currentChunk,
            $totalChunks,
            $log
        ) {
            $currentChunk++;

            $payload = [];

            foreach ($rows as $r) {
                $payload[] = [
                    'code'        => trim($r->Code),
                    'description' => trim($r->Description),
                ];
            }

            // Fast, safe bulk operation
            BuildingStyle::upsert(
                $payload,
                ['code'],          // unique key
                ['description']    // columns to update
            );

            // Update chunk progress
            $log->update([
                'current_chunk' => $currentChunk,
                'chunk_progress'=> (int) round(($currentChunk / $totalChunks) * 100),
                'message'       => "Building styles: chunk {$currentChunk} of {$totalChunks}",
            ]);
        });
    }
}

