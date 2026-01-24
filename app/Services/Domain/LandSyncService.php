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
            ->chunk(1000,function($rows){
                foreach($rows as $r){
                    Land::updateOrCreate(
                        ['acct'=>$r->acct],
                        ['land_use_code'=>$r->land_use,'land_area'=>$r->land_ar]
                    );
                }
            });
    }
}

