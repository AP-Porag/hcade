<?php

namespace App\Services\Domain;

use App\Models\Property;
use App\Models\SyncLog;
use App\Services\Domain\Contracts\ChunkAwareSync;
use Illuminate\Support\Facades\DB;


//->join(
//                'allowed_state_classes as asc',
//                DB::raw("asc.state_class_code COLLATE utf8mb4_unicode_ci"),
//                '=',
//                DB::raw("a.state_class COLLATE utf8mb4_unicode_ci")
//            )


class PropertySyncService implements ChunkAwareSync
{
    public function sync(int $taxYear, SyncLog $log): void
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $chunkSize    = 1000;
        $lastAcct     = $log->last_key; // NULL on first run
        $currentChunk = (int) $log->current_chunk;

        // Count once for progress visibility
        $totalRows = DB::table('raw_real_accts as a')
            ->join('allowed_state_classes as asc_tbl', 'asc_tbl.state_class_code', '=', 'a.state_class')
            ->where('asc_tbl.is_allowed', true)
            ->where('a.yr', $taxYear)
            ->count();

        if ($totalRows === 0) {
            $log->update([
                'chunk_progress' => 100,
                'message' => 'No properties found for selected tax year',
            ]);
            return;
        }

        $totalChunks = (int) ceil($totalRows / $chunkSize);

        $log->update([
            'total_chunks'   => $totalChunks,
            'current_chunk'  => $currentChunk,
            'chunk_progress' => 0,
            'message'        => 'Syncing properties',
        ]);

        while (true) {

            // 1️⃣ Fetch NEXT RANGE from SOURCE
            $sourceQuery = DB::table('raw_real_accts as a')
                ->join('allowed_state_classes as asc_tbl', 'asc_tbl.state_class_code', '=', 'a.state_class')
                ->where('asc_tbl.is_allowed', true)
                ->where('a.yr', $taxYear)
                ->when($lastAcct, fn ($q) => $q->where('a.acct', '>', $lastAcct))
                ->orderBy('a.acct')
                ->limit($chunkSize);

            $rows = $sourceQuery->get(['a.acct']);

            // ✅ REAL termination condition
            if ($rows->isEmpty()) {
                break;
            }

            $maxAcctInBatch = $rows->last()->acct;

            // 2️⃣ Insert / update EXACTLY that range
            DB::statement("
                INSERT INTO properties (
                    acct, tax_year,
                    site_addr_1, site_addr_2, site_addr_3,
                    mail_addr_1, mail_addr_2, mail_city, mail_state, mail_zip,
                    state_class, neighborhood_code, neighborhood_group,
                    market_area_1, market_area_2,
                    land_ar, bld_ar, acreage,
                    legal_description, is_active
                )
                SELECT
                    TRIM(a.acct), a.yr,
                    a.site_addr_1, a.site_addr_2, a.site_addr_3,
                    a.mail_addr_1, a.mail_addr_2, a.mail_city, a.mail_state, a.mail_zip,
                    a.state_class, a.neighborhood_code, a.neighborhood_grp,
                    a.market_area_1, a.market_area_2,
                    a.land_ar, a.bld_ar, a.acreage,
                    TRIM(CONCAT_WS(' ', a.lgl_1, a.lgl_2, a.lgl_3, a.lgl_4)),
                    1
                FROM raw_real_accts a
                JOIN allowed_state_classes asc_tbl
                    ON asc_tbl.state_class_code = a.state_class
                WHERE asc_tbl.is_allowed = 1
                  AND a.yr = ?
                  AND a.acct > ?
                  AND a.acct <= ?
                ON DUPLICATE KEY UPDATE
                    site_addr_1 = VALUES(site_addr_1),
                    site_addr_2 = VALUES(site_addr_2),
                    site_addr_3 = VALUES(site_addr_3),
                    mail_addr_1 = VALUES(mail_addr_1),
                    mail_addr_2 = VALUES(mail_addr_2),
                    mail_city   = VALUES(mail_city),
                    mail_state  = VALUES(mail_state),
                    mail_zip    = VALUES(mail_zip),
                    state_class = VALUES(state_class),
                    neighborhood_code = VALUES(neighborhood_code),
                    neighborhood_group = VALUES(neighborhood_group),
                    market_area_1 = VALUES(market_area_1),
                    market_area_2 = VALUES(market_area_2),
                    land_ar = VALUES(land_ar),
                    bld_ar  = VALUES(bld_ar),
                    acreage = VALUES(acreage),
                    legal_description = VALUES(legal_description),
                    is_active = VALUES(is_active)
            ", [$taxYear, $lastAcct ?? '', $maxAcctInBatch]);

            // 3️⃣ Advance safely
            $lastAcct = $maxAcctInBatch;
            $currentChunk++;

            $log->update([
                'current_chunk'  => $currentChunk,
                'chunk_progress' => (int) round(($currentChunk / $totalChunks) * 100),
                'last_key'       => $lastAcct,
                'message'        => "Properties synced up to acct {$lastAcct}",
            ]);

            if ($currentChunk % 25 === 0) {
                DB::disconnect();
                DB::reconnect();
            }
        }

        $log->update([
            'chunk_progress' => 100,
            'message' => 'Property synchronization completed',
        ]);
    }
}
