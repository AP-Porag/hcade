<?php

namespace App\Services\Domain;

use App\Models\MarketArea;
use Illuminate\Support\Facades\DB;

class MarketAreaSyncService
{
    public function sync()
    {
        DB::table('raw_desc_r_21_market_area')
            ->orderBy('code')
            ->chunk(500,function($rows){
                foreach($rows as $r){
                    MarketArea::updateOrCreate(
                        ['code'=>$r->mktarea],
                        ['description'=>$r->description]
                    );
                }
            });
    }
}

