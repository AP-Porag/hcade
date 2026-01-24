<?php

namespace App\Services\Domain;

use App\Models\LandUseCode;
use App\Models\Valuation;
use Illuminate\Support\Facades\DB;

class LandUseCodeSyncService
{
    public function sync()
    {
        DB::table('raw_desc_r_15_land_usecode')
            ->orderBy( 'code')
            ->chunk(500, function ($rows) {
                foreach ($rows as $r) {
                    LandUseCode::updateOrCreate(
                        ['code'=>$r->code],
                        ['description'=>$r->description]
                    );
                }
            });
    }
}

