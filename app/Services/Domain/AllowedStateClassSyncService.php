<?php

namespace App\Services\Domain;

use App\Models\AllowedStateClass;
use App\Models\SyncLog;
use App\Services\Domain\Contracts\ChunkAwareSync;
use Illuminate\Support\Facades\DB;

class AllowedStateClassSyncService implements ChunkAwareSync
{
    public function sync(int $taxYear, SyncLog $log): void
    {
        // Step 1: Get allowed state class codes from building styles
        $allowedCodes = DB::table('building_styles')
            ->where('is_allowed', true)
            ->whereNotNull('mapped_state_class')
            ->pluck('mapped_state_class')
            ->map(fn ($v) => trim($v))
            ->unique()
            ->values()
            ->all();

        $total = count($allowedCodes);

        $log->update([
            'total_chunks'   => $total,
            'current_chunk'  => 0,
            'chunk_progress' => 0,
            'message'        => 'Syncing allowed state classes',
        ]);

        if ($total === 0) {
            $log->update([
                'chunk_progress' => 100,
                'message'        => 'No allowed state classes found',
            ]);
            return;
        }

        // Step 2: Upsert allowed state classes
        $payload = [];
        $i = 0;

        foreach ($allowedCodes as $code) {
            $i++;

            $payload[] = [
                'state_class_code' => $code,
                'is_allowed'       => true,
            ];

            // batch insert every 100
            if ($i % 100 === 0 || $i === $total) {
                AllowedStateClass::upsert(
                    $payload,
                    ['state_class_code'],
                    ['is_allowed']
                );

                $payload = [];

                $log->update([
                    'current_chunk'  => $i,
                    'chunk_progress' => (int) round(($i / $total) * 100),
                    'message'        => "Allowed state classes: {$i} of {$total}",
                ]);
            }
        }
    }
}

