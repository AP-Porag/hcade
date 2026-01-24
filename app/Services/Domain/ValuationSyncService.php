<?php

namespace App\Services\Domain;

use App\Models\Valuation;
use Illuminate\Support\Facades\DB;

class ValuationSyncService
{
    public function sync(int $taxYear)
    {
        DB::table('raw_real_accts')
            ->where('yr',$taxYear)
            ->orderBy('acct')
            ->chunk(1000,function($rows) use ($taxYear){
                foreach($rows as $r){
                    Valuation::updateOrCreate(
                        ['acct'=>$r->acct,'tax_year'=>$taxYear],
                        [
                            'land_value'=>$r->land_val,
                            'building_value'=>$r->bld_val,
                            'market_value'=>$r->tot_mkt_val,
                            'appraised_value'=>$r->tot_appr_val
                        ]
                    );
                }
            });
    }
}

