<?php

namespace App\Services\Domain;

use App\Models\StateClass;
use App\Models\SyncLog;
use App\Services\Domain\Contracts\ChunkAwareSync;
use Illuminate\Support\Facades\DB;

class StateClassSyncService implements ChunkAwareSync
{
    public function sync(int $taxYear, SyncLog $log): void
    {
        $query = DB::table('raw_desc_r_01_state_class')
            ->orderBy('Code');

        $chunkSize   = 500;
        $totalRows   = $query->count();
        $totalChunks = (int) ceil($totalRows / $chunkSize);

        $log->update([
            'total_chunks'   => $totalChunks,
            'current_chunk'  => 0,
            'chunk_progress' => 0,
            'message'        => 'Syncing state classes',
        ]);

        $currentChunk = 0;

        $query->chunk($chunkSize, function ($rows) use (&$currentChunk, $totalChunks, $log) {
            $currentChunk++;

            $payload = [];

            foreach ($rows as $r) {
                $payload[] = [
                    'code'        => trim($r->Code),
                    'dept'        => trim($r->Dept),
                    'description' => trim($r->Description),
                ];
            }

            StateClass::upsert(
                $payload,
                ['code'],                    // unique key
                ['dept', 'description']      // update columns
            );

            $log->update([
                'current_chunk'  => $currentChunk,
                'chunk_progress' => (int) round(($currentChunk / $totalChunks) * 100),
                'message'        => "State classes: chunk {$currentChunk} of {$totalChunks}",
            ]);
        });
    }
}

