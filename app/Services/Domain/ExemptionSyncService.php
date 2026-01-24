<?php

namespace App\Services\Domain;

use App\Models\Exemption;
use Illuminate\Support\Facades\DB;

class ExemptionSyncService
{
    public function sync(int $taxYear)
    {
        DB::table('raw_jur_exempt')
            ->orderBy('acct')
            ->chunk(1000,function($rows){
                foreach($rows as $r){
                    Exemption::updateOrCreate(
                        ['acct'=>$r->acct,'exemption_code'=>$r->exempt_cat],
                        ['exemption_value'=>$r->exempt_val]
                    );
                }
            });
    }
}

