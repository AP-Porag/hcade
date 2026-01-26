<?php

namespace App\Services\Domain;

use App\Models\Land;
use Illuminate\Support\Facades\DB;

class LandSyncService
{
    public function sync(int $taxYear)
    {
        DB::table('raw_land')
            ->orderBy('acct')
            ->chunk(1000, function ($rows) {
                foreach ($rows as $r) {
                    Land::updateOrCreate(
                        [
                            'acct' => $r->acct,
                            'line_num' => $r->num, // important: multiple land rows per acct
                        ],
                        [
                            'land_use_code' => $r->use_cd,
                            'land_use_description' => $r->use_dscr,
                            'land_value' => $r->val,
                            'influence_code' => $r->inf_cd,
                            'influence_description' => $r->inf_dscr,
                            'override_description' => $r->ovr_dscr,
                        ]
                    );
                }
            });
    }
}

